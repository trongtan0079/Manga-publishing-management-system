# 🕵️ BÁO CÁO WORKFLOW AUDIT TOÀN BỘ HỆ THỐNG
### Dưới Góc Nhìn Của Senior Software Engineer & Hội Đồng Bảo Vệ Đồ Án

---

> [!NOTE]
> Báo cáo này thực hiện kiểm toán toàn bộ luồng quy trình nghiệp vụ (Workflow Audit) dựa trên mã nguồn hiện tại của dự án. Tài liệu tập trung làm rõ kiến trúc vận hành, dữ liệu trạng thái, kiểm soát truy cập, cơ chế xử lý xung đột và dự đoán các câu hỏi phản biện từ Hội đồng chấm đồ án.

---

## 🔍 1. KIỂM TOÁN CHI TIẾT TỪNG WORKFLOW NGHIỆP VỤ

### 👤 1.1. WORKFLOW 1: Quản Trị Người Dùng & Phân Quyền (Admin Role)

* **Mục tiêu nghiệp vụ:** Quản lý vòng đời tài khoản người dùng (tạo mới, chỉnh sửa thông tin, khóa/mở khóa tài khoản) và phân quyền truy cập thông qua các Role.
* **Quy trình từ đầu đến cuối:**
  1. Admin đăng nhập -> Vào trang Quản lý Thành viên.
  2. Bấm "Thêm người dùng mới" -> Nhập thông tin (username, email, password, full_name, role_id, status) -> Nhấn Lưu.
  3. Khi cần thiết, Admin bấm Chỉnh sửa thông tin người dùng hoặc đổi trạng thái (Active, Inactive, Banned).
* **Chi tiết luồng kỹ thuật:**
  * **Trigger:** Admin gửi yêu cầu HTTP POST/GET từ giao diện quản trị.
  * **Controller & Action:**
    * `UserController::index` (Hiển thị danh sách)
    * `UserController::create` / `store` (Hiển thị form & Lưu tài khoản mới)
    * `UserController::edit` / `update` (Hiển thị form chỉnh sửa & Cập nhật)
    * `UserController::delete` (Xóa tài khoản)
  * **Model sử dụng:** `User`, `Role`
  * **Database tables liên quan:** `users`, `roles`
  * **Dữ liệu được thay đổi:** Thêm mới (INSERT), cập nhật (UPDATE), hoặc xóa (DELETE) dòng dữ liệu trong bảng `users`.
  * **Trạng thái (status) thay đổi:** Cột `users.status` chuyển đổi giữa `active`, `inactive`, và `banned`.
  * **Notification được tạo:** Không có (tác vụ của admin được thực hiện trực tiếp, không qua cơ chế thông báo nội bộ).
  * **Validation thực hiện:**
    * Kiểm tra trùng lặp `username` và `email` trước khi thêm/sửa.
    * Xác thực định dạng email hợp lệ bằng `FILTER_VALIDATE_EMAIL`.
    * Kiểm tra độ dài mật khẩu tối thiểu 6 ký tự.
    * Kiểm tra tính tồn tại của `role_id` trong bảng `roles`.
  * **Quyền truy cập (Authorization):** Bắt buộc `session.role_name === 'admin'` thông qua hàm `requireRole('admin')` ở constructor của `UserController`.
* **Xử lý xung đột và Lỗi hệ thống:**
  * **Trường hợp lỗi:** Nhập trùng email/username đã tồn tại; CSDL lỗi kết nối.
  * **Xung đột dữ liệu (Potential Conflict):** Hai Admin cùng lúc cập nhật trạng thái của một người dùng.
  * **Cách xử lý hiện tại:** Hệ thống sử dụng mô hình *Last-write wins* (người gửi sau ghi đè người trước) phối hợp với cơ chế Khóa dòng (Row Locking) tự động của MySQL InnoDB.
* **Điểm thiếu sót:** Chưa có log lịch sử hoạt động (Activity Log) để ghi nhận Admin nào đã thực hiện thay đổi trên tài khoản nào.

---

### ✍️ 1.2. WORKFLOW 2: Khởi Tạo & Quản Lý Tác Phẩm (Mangaka Role)

* **Mục tiêu nghiệp vụ:** Cho phép tác giả sáng tác lập hồ sơ tác phẩm (Series), tổ chức các chương truyện (Chapters) và tải lên các trang truyện phác thảo (Pages) để chuẩn bị cho quy trình sản xuất.
* **Quy trình từ đầu đến cuối:**
  1. Mangaka đăng nhập -> Bấm "Tạo bộ truyện mới" -> Nhập tên truyện, mô tả, ảnh bìa -> Lưu.
  2. Tại trang chi tiết bộ truyện -> Bấm "Thêm Chapter" -> Nhập số chương, tiêu đề -> Lưu.
  3. Tại trang chi tiết Chapter -> Upload từng trang bản vẽ phác thảo (`Page`) với số trang tương ứng.
* **Chi tiết luồng kỹ thuật:**
  * **Trigger:** Người dùng Mangaka kích hoạt gửi Form qua HTTP POST.
  * **Controller & Action:**
    * `SeriesController::create` / `store` / `edit` / `update` / `delete` / `show`
    * `ChapterController::create` / `store` / `edit` / `update` / `delete`
    * `PageController::create` / `store` / `delete`
  * **Model sử dụng:** `Series`, `Chapter`, `Page`, `Task`
  * **Database tables liên quan:** `series`, `chapters`, `pages`
  * **Dữ liệu được thay đổi:** Thêm, cập nhật, hoặc xóa các dòng tương ứng trong bảng `series`, `chapters`, `pages`.
  * **Trạng thái (status) thay đổi:**
    * `series.status`: Thay đổi từ `planning` (lập kế hoạch) -> `ongoing` (đang tiến hành) -> `completed`/`canceled`/`suspended`.
    * `chapters.status`: Chuyển từ `drafting` (nháp) -> `drawing` (đang vẽ) -> `reviewing` -> `approved` -> `published`.
    * `pages.status`: Chuyển từ `sketch` (phác thảo) -> `inked` (đi nét) -> `toned` (đổ tone) -> `finished` (hoàn thành).
  * **Notification được tạo:** Không có.
  * **Validation thực hiện:**
    * Tiêu đề bộ truyện/chương truyện không được trống và không vượt quá 255 ký tự.
    * `chapter_number` phải là số nguyên dương lớn hơn 0 và không trùng lặp trong cùng một `series` (ràng buộc `UNIQUE` ở mức DB).
    * `page_number` phải là số nguyên dương lớn hơn 0 và không trùng lặp trong một `chapter` (ràng buộc `UNIQUE`).
    * File ảnh tải lên bắt buộc phải nhỏ hơn 2MB, thuộc định dạng `jpg, jpeg, png, webp` và được kiểm tra MIME type thực tế bằng `finfo_file`.
  * **Quyền truy cập (Authorization):** Kiểm tra `requireRole('mangaka')`. Thực hiện kiểm tra quyền sở hữu đối với từng thực thể (`checkSeriesOwnership`, `checkChapterOwnership`) để đảm bảo tác giả không thể can thiệp vào truyện của người khác.
* **Xử lý xung đột và Lỗi hệ thống:**
  * **Trường hợp lỗi:** File upload vượt quá dung lượng; định dạng file không hợp lệ; trùng lặp số chương trong cùng bộ truyện.
  * **Xung đột dữ liệu:** Tác giả cố tình xóa bộ truyện trong khi các chương truyện đang có dữ liệu liên kết hoặc đang trong quá trình kiểm duyệt.
  * **Cách xử lý hiện tại:** Ràng buộc khóa ngoại ở mức Database (`ON DELETE CASCADE` cho chapters/pages và `ON DELETE RESTRICT` ở một số bảng liên quan) giúp bảo vệ tính toàn vẹn dữ liệu, hệ thống sẽ ném ra ngoại lệ PDOException và thông báo lỗi thân thiện cho tác giả thay vì làm sập ứng dụng.
* **Điểm thiếu sót:** Chưa hỗ trợ tính năng upload hàng loạt ảnh (Bulk Page Upload), tác giả phải upload từng trang truyện riêng lẻ.

---

### 🎨 1.3. WORKFLOW 3: Phân Công & Nghiệm Thu Công Việc Trợ Lý (Mangaka & Assistant Role)

* **Mục tiêu nghiệp vụ:** Tác giả chính (Mangaka) giao việc (Task) như đi nét, đổ tone trên từng trang truyện cụ thể cho trợ lý (Assistant) để tối ưu tiến độ sản xuất của studio và nghiệm thu sản phẩm nộp lên.
* **Quy trình từ đầu đến cuối:**
  1. Mangaka vào trang chi tiết trang truyện (`Page`) -> Bấm "Giao việc" -> Chọn Assistant, nhập tiêu đề, mô tả yêu cầu, deadline -> Nhấn Giao.
  2. Assistant nhận thông báo -> Vào xem danh sách Task được giao -> Tải file ảnh gốc về máy làm việc -> Upload bản vẽ hoàn thiện lên hệ thống (`Submission`).
  3. Mangaka nhận thông báo nộp bài -> Vào chi tiết Submission -> Tạo `Review` đánh giá (chấm điểm rating, ghi bình luận chỉnh sửa) và chọn "Phê duyệt" (Approve) hoặc "Từ chối" (Reject).
* **Chi tiết luồng kỹ thuật:**
  * **Trigger:** Mangaka giao Task (POST), Assistant nộp sản phẩm (POST), Mangaka phê duyệt (POST).
  * **Controller & Action:**
    * `TaskController::create` / `store` / `show` / `edit` / `update` / `delete`
    * `SubmissionController::create` / `store` (Assistant nộp bài)
    * `ReviewController::create` / `store` (Mangaka đánh giá & phê duyệt)
  * **Model sử dụng:** `Task`, `Page`, `Submission`, `Review`, `Notification`, `User`
  * **Database tables liên quan:** `tasks`, `submissions`, `reviews`, `notifications`, `pages`
  * **Dữ liệu được thay đổi:** INSERT vào bảng `tasks`, `submissions`, `reviews`, `notifications`. UPDATE bảng `tasks` (trạng thái), `pages` (trạng thái trang).
  * **Trạng thái (status) thay đổi:**
    * `tasks.status`: Từ `pending` -> `in_progress` -> `completed` (khi submission được duyệt).
    * `submissions.status`: Từ `pending` -> `approved` hoặc `rejected`.
  * **Notification được tạo:**
    * Tạo notification cho Assistant khi Mangaka giao task mới.
    * Tạo notification cho Mangaka khi Assistant nộp bài (`Submission`).
    * Tạo notification cho Assistant khi Mangaka duyệt/từ chối bản vẽ (`Review`).
  * **Validation thực hiện:**
    * Tên task và do_date không được trống.
    * Kiểm tra Assistant được giao có thực sự tồn tại trong hệ thống hay không.
    * Khi Assistant nộp bài, kiểm tra xem Task đó có thuộc quyền sở hữu của họ và đang ở trạng thái chưa hoàn thành hay không.
    * Điểm chấm đánh giá (`rating`) phải nằm trong khoảng từ 1 đến 5.
  * **Quyền truy cập (Authorization):** Hàm `requireLogin()`. Kiểm tra quyền sở hữu công việc đối với Assistant (`task.assistant_id == user_id`) và Mangaka (`task.mangaka_id == user_id`).
* **Xử lý xung đột và Lỗi hệ thống:**
  * **Trường hợp lỗi:** File nộp bị lỗi tải lên; nộp bài quá deadline quy định.
  * **Xung đột dữ liệu:** Assistant nộp bài cùng lúc Mangaka đang tiến hành sửa đổi hoặc xóa Task đó.
  * **Cách xử lý hiện tại:** Nhờ ràng buộc cascade mức DB, nếu Mangaka xóa Task, mọi bản ghi `submissions` và `reviews` liên kết sẽ tự động được dọn dẹp sạch sẽ để tránh bản ghi rác trong cơ sở dữ liệu.
* **Điểm thiếu sót:** Chưa có kênh trao đổi trực tiếp (chat/comment) dưới dạng hội thoại giữa Tác giả và Trợ lý dưới mỗi Task mà chỉ là các nhận xét tĩnh một chiều qua mỗi lần Review.

---

### 📝 1.4. WORKFLOW 4: Kiểm Duyệt Bản Thảo Chương Truyện (Mangaka & Tantou Editor Role)

* **Mục tiêu nghiệp vụ:** Mangaka nộp toàn bộ bản thảo hoàn thiện của Chương truyện (`Chapter`) để biên tập viên phụ trách (Tantou Editor) đọc và đánh giá chất lượng (nội dung, thoại, kịch bản) trước khi đưa ra Hội đồng phê duyệt xuất bản.
* **Quy trình từ đầu đến cuối:**
  1. Mangaka hoàn thiện các trang truyện -> Vào mục Bản thảo -> Chọn Chapter cần nộp -> Tải lên file nén bản thảo (.zip/.pdf) -> Nhấn Giao.
  2. Bản thảo đi vào hàng đợi chung. Editor đăng nhập -> Vào trang Quản lý Bản thảo -> Bấm kiểm duyệt bản thảo đang chờ xử lý.
  3. Editor kiểm tra bản vẽ -> Nhập đánh giá, ý kiến nhận xét -> Bấm "Phê duyệt" (Approve) hoặc "Yêu cầu chỉnh sửa" (Reject).
* **Chi tiết luồng kỹ thuật:**
  * **Trigger:** Mangaka submit file bản thảo (POST) -> Editor gửi kết quả đánh giá (POST).
  * **Controller & Action:**
    * `SubmissionController::index` (Editor xem hàng đợi)
    * `SubmissionController::create` / `store` (Mangaka nộp bản thảo)
    * `ReviewController::create` / `store` (Editor viết đánh giá)
  * **Model sử dụng:** `Submission`, `Review`, `Chapter`, `Notification`
  * **Database tables liên quan:** `submissions`, `reviews`, `chapters`, `notifications`
  * **Dữ liệu được thay đổi:** INSERT vào bảng `submissions`, `reviews`, `notifications`. UPDATE bảng `submissions` và `chapters` (trạng thái).
  * **Trạng thái (status) thay đổi:**
    * `submissions.status`: Chuyển từ `pending` -> `approved` hoặc `rejected`.
    * `chapters.status`: Chuyển từ `reviewing` -> `approved` (nếu duyệt thành công).
  * **Notification được tạo:** Tạo notification gửi tới Mangaka ngay khi Editor lưu ý kiến đánh giá để thông báo kết quả phê duyệt kèm trích dẫn nhận xét.
  * **Validation thực hiện:**
    * Bắt buộc chọn đúng Chapter và file upload không được trống.
    * File nộp phải thuộc định dạng đóng gói/bản thảo quy định.
    * Editor không được để trống ô nhập ý kiến nhận xét (`comments`).
  * **Quyền truy cập (Authorization):** Mangaka chỉ được phép nộp bản thảo thuộc các bộ truyện do mình sáng tác. Editor được phép xem toàn bộ danh sách bản thảo chờ duyệt trên hệ thống (mô hình Shared Queue).
* **Xử lý xung đột và Lỗi hệ thống:**
  * **Trường hợp lỗi:** File bản thảo bị lỗi trong quá trình tải lên máy chủ.
  * **Xung đột dữ liệu:** Hai Editor cùng mở một bản thảo pending và cùng bấm gửi ý kiến phê duyệt gần như đồng thời.
  * **Cách xử lý hiện tại:** Logic kiểm soát trạng thái ở Controller sẽ chặn người gửi sau bằng cách đối chiếu trạng thái thực tế trong DB: nếu trạng thái bản thảo đã chuyển từ `pending` sang `approved/rejected`, hệ thống sẽ từ chối lưu review của Editor thứ hai và thông báo lỗi.
* **Điểm thiếu sót:** Chưa hỗ trợ tính năng chỉ định biên tập viên phụ trách riêng cho từng bộ truyện (Tantou Editor Assignment), dẫn đến bất kỳ Editor nào cũng có quyền duyệt bài của các bộ truyện khác.

---

### 🏛️ 1.5. WORKFLOW 5: Đánh Giá Xếp Hạng & Xuất Bản Định Kỳ (Editorial Board Role)

* **Mục tiêu nghiệp vụ:** Hội đồng biên tập (Editorial Board) thu thập điểm số bình chọn của độc giả, tiến hành lập bảng xếp hạng các bộ truyện theo chu kỳ (tuần/tháng) và ra quyết định chính thức về việc xuất bản rộng rãi hoặc đình bản tác phẩm hiệu suất kém.
* **Quy trình từ đầu đến cuối:**
  1. Thành viên hội đồng đăng nhập -> Truy cập mục Bảng xếp hạng.
  2. Bấm "Tạo xếp hạng mới" -> Chọn bộ truyện, nhập điểm số đánh giá, thứ hạng và kỳ đánh giá (Period Start Date) -> Nhấn Lưu.
  3. Ban điều hành vào trang Duyệt xuất bản -> Chọn bộ truyện mong muốn -> Cập nhật trạng thái tác phẩm thành `ongoing` (xuất bản) hoặc `canceled`/`suspended` (đình bản).
* **Chi tiết luồng kỹ thuật:**
  * **Trigger:** Board member gửi Form đánh giá xếp hạng hoặc cập nhật trạng thái tác phẩm thông qua HTTP POST.
  * **Controller & Action:**
    * `SeriesRankingController::create` / `store` / `edit` / `update` / `delete`
    * `SeriesController::publish` (Xem danh sách duyệt) / `updateStatus` (Cập nhật trạng thái truyện)
  * **Model sử dụng:** `SeriesRanking`, `Series`, `Notification`
  * **Database tables liên quan:** `series_rankings`, `series`, `notifications`
  * **Dữ liệu được thay đổi:** INSERT/UPDATE/DELETE dòng dữ liệu trong bảng `series_rankings`. UPDATE cột trạng thái trong bảng `series`.
  * **Trạng thái (status) thay đổi:**
    * `series.status`: Thay đổi sang `ongoing` (duyệt xuất bản) hoặc chuyển thành `canceled` (đình bản do thứ hạng quá thấp) / `suspended` (tạm dừng).
  * **Notification được tạo:** Tự động tạo và gửi thông báo hệ thống đến tài khoản của Mangaka sở hữu bộ truyện để thông báo vị trí thứ hạng cụ thể và điểm số đạt được trong kỳ đánh giá vừa công bố.
  * **Validation thực hiện:**
    * Thứ hạng (`rank_position`) phải lớn hơn hoặc bằng 1.
    * Điểm số (`score`) bắt buộc nằm trong khoảng hợp lệ từ 0 đến 100.
    * Định dạng kỳ đánh giá (`period_start_date`) phải là chuỗi ngày hợp lệ.
    * Kiểm tra trùng lặp xếp hạng: Không được phép có hai bản ghi xếp hạng cho cùng một bộ truyện trong cùng một kỳ đánh giá (`checkDuplicateRanking`).
  * **Quyền truy cập (Authorization):** Bắt buộc người dùng phải có vai trò `board` thông qua cơ chế `requireRole('board')` trước khi gọi các Action ghi/xóa dữ liệu.
* **Xử lý xung đột và Lỗi hệ thống:**
  * **Trường hợp lỗi:** Nhập điểm số ngoài khoảng cho phép; Cố tình xếp hạng trùng kỳ.
  * **Xung đột dữ liệu:** Hai thành viên hội đồng cùng chấm điểm xếp hạng cho một bộ truyện trong cùng một tuần/tháng.
  * **Cách xử lý hiện tại:** Bảng `series_rankings` áp dụng logic kiểm tra trùng lặp tại tầng Model trước khi thực hiện câu lệnh INSERT. Nếu phát hiện bộ truyện đã được chấm điểm trong kỳ đó, hệ thống sẽ chặn lại và báo lỗi.
* **Điểm thiếu sót:** Điểm số và thứ hạng của truyện đang được nhập thủ công hoàn toàn từ nhận định của hội đồng, chưa có công cụ hỗ trợ tự động tính toán điểm dựa trên lượt xem thực tế hoặc tổng hợp từ phiếu bầu số hóa của độc giả.

---

## 📊 2. BẢNG TỔNG HỢP KẾT QUẢ KIỂM TOÁN HỆ THỐNG (WORKFLOW AUDIT SUMMARY)

Do bảng tổng hợp chứa lượng thông tin kỹ thuật rất lớn, để tránh tình trạng vỡ khung và khó đọc trên giao diện Markdown, tài liệu phân tách thành 3 bảng chuyên biệt dưới đây:

### BẢNG A: CHI TIẾT ĐIỀU PHỐI VÀ LUỒNG ĐIỀU KHIỂN (CONTROL FLOW)

| Tên Quy Trình (Workflow) | Trigger (Sự Kiện Kích Hoạt) | Controller | Action | Model Liên Quan | Tác Nhân Tiếp Theo (Next Actor) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Quản trị người dùng** | Gửi form thông tin user mới | `UserController` | `create`, `store` | `User`, `Role` | Hệ thống (Đăng nhập) |
| **Quản lý tác phẩm** | Đăng ký truyện & chapter mới | `SeriesController`, `ChapterController` | `store` | `Series`, `Chapter` | Tác giả (Upload trang) |
| **Giao việc trợ lý** | Giao nhiệm vụ vẽ/hoàn thiện | `TaskController` | `store` | `Task`, `Page` | Assistant (Nhận việc) |
| **Nộp bài trợ lý** | Assistant tải lên file sản phẩm | `SubmissionController` | `store` | `Submission`, `Task` | Mangaka (Kiểm duyệt) |
| **Duyệt bài trợ lý** | Mangaka chấm điểm và ghi ý kiến | `ReviewController` | `store` | `Review`, `Submission` | Hệ thống (Đổi trạng thái) |
| **Nộp bản thảo** | Mangaka gửi file chapter (.zip) | `SubmissionController` | `store` | `Submission`, `Chapter` | Tantou Editor (Kiểm duyệt) |
| **Duyệt bản thảo** | Editor gửi đánh giá chương | `ReviewController` | `store` | `Review`, `Submission` | Editorial Board (Xếp hạng) |
| **Đánh giá xếp hạng**| Board member công bố bảng hạng | `SeriesRankingController` | `store` | `SeriesRanking` | Mangaka (Xem kết quả) |
| **Duyệt xuất bản** | Board member quyết định xuất bản | `SeriesController` | `updateStatus` | `Series` | Độc giả (Xem truyện) |

---

### BẢNG B: CHI TIẾT THAY ĐỔI DỮ LIỆU VÀ TRẠNG THÁI (DATA & STATE)

| Tên Quy Trình (Workflow) | Bảng CSDL Ảnh Hưởng | Dữ Liệu Thay Đổi | Trạng Thái Thay Đổi | Thông Báo (Notification) | Kiểm Thực Dữ Liệu (Validation) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Quản trị người dùng** | `users` | INSERT/UPDATE/DELETE | `status`: active/inactive/banned | Không có | Trùng username/email, định dạng email, độ dài pass |
| **Quản lý tác phẩm** | `series`, `chapters` | INSERT/UPDATE/DELETE | `series.status`, `chapters.status` | Không có | Tiêu đề không rỗng, chương là số dương và không trùng |
| **Giao việc trợ lý** | `tasks` | INSERT mới dòng Task | `tasks.status` = 'pending' | Gửi cho Assistant | Tiêu đề, deadline không trống, assistant phải tồn tại |
| **Nộp bài trợ lý** | `submissions` | INSERT mới dòng nộp bài | `submissions.status` = 'pending' | Gửi cho Mangaka | Task phải khớp với trợ lý, file nộp không trống |
| **Duyệt bài trợ lý** | `reviews`, `tasks` | INSERT review, UPDATE task | `tasks.status` = 'completed' | Gửi cho Assistant | Rating phải nằm trong khoảng 1-5, note không trống |
| **Nộp bản thảo** | `submissions` | INSERT bản thảo mới | `chapters.status` = 'reviewing' | Không có | Đúng chapter của tác giả, file không trống |
| **Duyệt bản thảo** | `reviews`, `chapters` | INSERT review, UPDATE chapter | `chapters.status` = 'approved' | Gửi cho Mangaka | Nhận xét không trống, rating hợp lệ |
| **Đánh giá xếp hạng**| `series_rankings` | INSERT bản ghi ranking | Không có | Gửi cho Mangaka | Hạng >= 1, score 0-100, tránh trùng lặp kỳ chấm |
| **Duyệt xuất bản** | `series` | UPDATE trạng thái truyện | `series.status` = ongoing/canceled | Không có | Status phải nằm trong danh sách được phép |

---

### BẢNG C: KIỂM SOÁT XUNG ĐỘT VÀ ĐIỂM THIẾU SÓT (EXCEPTION HANDLING)

| Tên Quy Trình (Workflow) | Xung Đột Dữ Liệu Tiềm Ẩn | Cách Hệ Thống Xử Lý Hiện Tại | Điểm Còn Thiếu Trong Quy Trình |
| :--- | :--- | :--- | :--- |
| **Quản trị người dùng** | Hai admin cùng cập nhật 1 user | Ghi đè người sau (Last-write wins), lock dòng | Thiếu nhật ký ghi lại lịch sử thao tác của Admin |
| **Quản lý tác phẩm** | Xóa truyện khi đang có chapter | Sử dụng ràng buộc CSDL để chặn hoặc xóa cascade | Chưa hỗ trợ upload hàng loạt trang vẽ phác thảo |
| **Giao việc trợ lý** | Xóa Task khi trợ lý đang làm việc | Xóa cascade các submissions/reviews liên quan | Chưa có khung chat thảo luận nhanh dưới mỗi Task |
| **Duyệt bài trợ lý** | Duyệt trùng một bản nộp task | Chỉ cho phép đánh giá khi trạng thái là 'pending' | Chưa tự động cập nhật tiến độ tổng hợp studio |
| **Duyệt bản thảo** | Hai Editor cùng duyệt một chương | Khóa trạng thái, người gửi sau bị Controller chặn | Chưa gán Biên tập viên phụ trách riêng cho Series |
| **Đánh giá xếp hạng**| Hai Board chấm trùng kỳ cho một truyện | Chặn tại tầng Model bằng hàm kiểm tra trùng lặp | Điểm số phải nhập tay, chưa tính tự động |
| **Duyệt xuất bản** | Thay đổi trạng thái về planning | Kiểm tra danh sách allowed status | Chưa liên kết trực tiếp với dữ liệu phát hành vật lý |

---

## 🏆 3. ĐÁNH GIÁ ĐỘ HOÀN THIỆN & CẨM NANG BẢO VỆ ĐỒ ÁN

### 📊 3.1. Đánh giá mức độ hoàn thiện (Thang điểm 10)

1. **Workflow Quản trị người dùng & Phân quyền:** **9.0 / 10**
   * *Đánh giá:* Thiết kế chuẩn mực, phân quyền chặt chẽ, đầy đủ tính năng CRUD và kiểm thực dữ liệu. Chỉ thiếu phần log lịch sử hoạt động.
2. **Workflow Khởi tạo & Quản lý tác phẩm:** **8.5 / 10**
   * *Đánh giá:* Luồng dữ liệu rõ ràng, cấu trúc liên kết Series - Chapter - Page chặt chẽ. Điểm trừ nhỏ là trải nghiệm người dùng khi phải upload từng trang truyện.
3. **Workflow Phân công & Nghiệm thu công việc trợ lý:** **9.5 / 10**
   * *Đánh giá:* Đây là luồng nghiệp vụ tốt nhất của hệ thống. Đầy đủ các bước trung gian, tương tác hai chiều Mangaka - Assistant rất rõ ràng, hệ thống thông báo tự động (Notification) hoạt động chính xác.
4. **Workflow Kiểm duyệt bản thảo chương truyện:** **8.0 / 10**
   * *Đánh giá:* Hoạt động ổn định về mặt kỹ thuật, xử lý tốt xung đột đồng thời của Editor. Tuy nhiên việc thiết kế theo mô hình hàng đợi mở (Shared Queue) làm giảm tính bảo mật chuyên môn hóa.
5. **Workflow Đánh giá xếp hạng & Xuất bản:** **7.5 / 10**
   * *Đánh giá:* Hoàn thành tốt việc tổng hợp và công bố thứ hạng, có thông báo tự động. Tuy nhiên, tính năng này còn phụ thuộc nhiều vào việc nhập liệu thủ công của con người (human input).

---

### ⚠️ 3.2. Workflow có nguy cơ cao bị Hội đồng chất vấn
* **Workflow 4 (Kiểm duyệt bản thảo chương truyện):** Mô hình **Shared Queue** của các Editor chắc chắn sẽ bị hỏi vì nó khác với thực tế là mỗi tác giả chỉ làm việc với một biên tập viên đại diện (Tantou Editor) duy nhất.
* **Workflow 5 (Đánh giá xếp hạng):** Cách tính điểm và xếp hạng thủ công dễ bị coi là thiếu thực tế khi vận hành ở quy mô lớn nếu không có thuật toán tự động tính điểm dựa trên hành vi độc giả.

---

### 🎓 3.3. Bộ câu hỏi phản biện của Hội đồng và Gợi ý trả lời

> [!TIP]
> ### 🗣️ Câu Hỏi 1: Tại sao hệ thống lại cho phép mọi Editor cùng nhìn thấy và duyệt bản thảo của tất cả các bộ truyện mà không gán cố định Editor phụ trách?
> **Gợi ý trả lời thuyết phục:**
> *"Dạ thưa Hội đồng, thiết kế này dựa trên nguyên lý **Shared Queue (Hàng đợi chia sẻ)**. Trong môi trường các nhà xuất bản manga quy mô vừa và nhỏ, việc chia sẻ hàng đợi giúp ban biên tập hoạt động linh hoạt, không bị gián đoạn quy trình xuất bản nếu biên tập viên phụ trách chính nghỉ phép hoặc quá tải. 
> 
> Về mặt kỹ thuật, hệ thống ghi nhận chính xác tài khoản Editor nào đã phê duyệt thông qua trường `reviewer_id` trong bảng `reviews`. Đối với định hướng mở rộng cho tòa soạn lớn, chúng em hoàn toàn có thể bổ sung trường `editor_id` vào bảng `series` để lọc danh sách bản thảo theo đúng tài khoản phụ trách mà không cần thay đổi cấu trúc bảng `submissions` hay `reviews` hiện tại."*

> [!WARNING]
> ### 🗣️ Câu Hỏi 2: Làm thế nào để đảm bảo hai thành viên Editorial Board không nhập trùng hoặc làm sai lệch kết quả xếp hạng của một bộ truyện trong cùng một kỳ đánh giá?
> **Gợi ý trả lời thuyết phục:**
> *"Dạ, hệ thống đã ngăn ngừa lỗi này bằng cơ chế kiểm tra trùng lặp tại Model `SeriesRanking` qua hàm `checkDuplicateRanking`. Trước khi thực hiện ghi dữ liệu xếp hạng mới vào CSDL, hệ thống sẽ thực hiện một câu lệnh SELECT truy vấn xem đã có bản ghi xếp hạng nào của `series_id` đó trùng với ngày bắt đầu kỳ đánh giá `period_start_date` hay chưa. Nếu đã có, hệ thống sẽ ném ra ngoại lệ và chặn việc ghi đè dữ liệu. Đồng thời, CSDL cũng thiết lập khóa ngoại và quyền ghi chặt chẽ chỉ dành riêng cho tài khoản có vai trò là `board`."*

> [!NOTE]
> ### 🗣️ Câu Hỏi 3: Trạng thái của trang truyện (`pages.status`) và trạng thái của chương truyện (`chapters.status`) liên kết với nhau như thế nào trong quy trình từ phác thảo đến hoàn thiện?
> **Gợi ý trả lời thuyết phục:**
> *"Dạ thưa Thầy/Cô, hai trạng thái này đại diện cho hai mức độ quản lý khác nhau:
> 1. `pages.status` quản lý quy trình sản xuất nội bộ trong studio của Mangaka (sketch -> inked -> toned -> finished) thông qua các công việc giao cho Assistant.
> 2. `chapters.status` quản lý quy trình ở cấp độ xuất bản (drafting -> drawing -> reviewing -> approved -> published).
> 
> Khi tất cả các trang truyện của một chương được vẽ xong (hoàn thành các task phụ), chương truyện sẽ được chuyển sang trạng thái chờ duyệt (`reviewing`) và gửi lên cho Editor. Khi Editor phê duyệt bản thảo chương đó thành công, trạng thái chương truyện tự động cập nhật thành `approved` thông qua transaction ở `ReviewController::store`."*
