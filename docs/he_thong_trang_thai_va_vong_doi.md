# 🧭 HỆ THỐNG TRẠNG THÁI VÀ VÒNG ĐỜI NGHIỆP VỤ (MANGA PMS)

Tài liệu này đặc tả chi tiết toàn bộ hệ thống trạng thái (lifecycles), ý nghĩa nghiệp vụ và các cơ chế kích hoạt kỹ thuật dưới tầng mã nguồn của 4 đối tượng cốt lõi trong hệ thống Manga PMS.

---

## 📚 1. SERIES (BỘ TRUYỆN)
Quản lý vòng đời phát triển vĩ mô của dự án truyện Manga từ khi còn là ý tưởng phác thảo đến khi phát hành thương mại hoặc đình bản.
*   **Bảng dữ liệu:** `series`
*   **Trường trạng thái trong CSDL:** `status` và `publish_type`

---

### 1.1. Nháp (Chưa nộp)
*   **Giá trị CSDL**: `status = 'planning'`, `publish_type = 'draft'`
*   **Ý nghĩa**: Ý tưởng sơ bộ mới tạo của tác giả. Trạng thái này là không gian riêng tư của Mangaka để chỉnh sửa cốt truyện, tải lên ảnh bìa hoặc tài liệu đề xuất.
*   **Bảo mật**: Ẩn hoàn toàn khỏi màn hình của Editor và Board. Hệ thống chặn đứng các truy cập trực tiếp bằng đường dẫn (URL Bypass).
*   **Kích hoạt**: Khi Mangaka bấm nút **"Tạo Series mới"** [SeriesController::store()].

---

### 1.2. Chờ phê duyệt
*   **Giá trị CSDL**: `status = 'planning'`, `publish_type = 'submitted'`
*   **Ý nghĩa**: Đề xuất bộ truyện đã được gửi lên hệ thống và hiển thị công khai trên Dashboard của Hội đồng biên tập (Board) để chờ bỏ phiếu.
*   **Ràng buộc**: Tác giả bị khóa tạm thời quyền chỉnh sửa hồ sơ truyện khi đang trong trạng thái chờ duyệt.
*   **Kích hoạt**: Khi Mangaka bấm nút **"Nộp Đề Xuất"** tại trang chi tiết bộ truyện [SeriesController::submit()].

---

### 1.3. Đang phát hành (Đang triển khai)
*   **Giá trị CSDL**: `status = 'ongoing'`
*   **Ý nghĩa**: Trạng thái này có sự phân tách rõ rệt về mặt ngữ nghĩa tùy theo góc nhìn:
    1.  **Góc nhìn độc giả (Public)**: "Đang phát hành" nghĩa là bộ truyện đã được giới thiệu ra thị trường và vẫn đang tiếp tục ra các chương (chapter) mới định kỳ (ví dụ: One Piece, Spy × Family).
    2.  **Góc nhìn nhà xuất bản (Nội bộ hệ thống PMS)**: Khi đề xuất bộ truyện được phê duyệt, dự án lập tức chuyển sang trạng thái `ongoing` (Đang triển khai). Lúc này, bộ truyện đã chính thức trở thành một dự án sản xuất hoạt động của tòa soạn, Mangaka bắt đầu viết kịch bản và phân công công việc. Dù tại thời điểm này có thể chưa có chương nào được in ấn hay phát hành ra công chúng, dự án đã bắt đầu hoạt động và chạy tiến độ trong quy trình sản xuất nội bộ.
*   **Ràng buộc**: Board bắt buộc phải thiết lập chu kỳ phát hành của Series (Hàng tuần hoặc Hàng tháng) và gán Biên tập viên chuyên trách (Tantou Editor) quản lý bộ truyện. Chu kỳ này là kế hoạch phát hành dự kiến (kỳ vọng), làm cơ sở lập lịch sản xuất, deadline và theo dõi tiến độ; không phải là thời điểm phát hành bắt buộc hay tự động của từng Chapter.
*   **Kích hoạt**: Khi Hội đồng (Board) chốt quyết định phê duyệt [SeriesController::updateStatus()].
    *   *Điều kiện chốt:* Toàn bộ thành viên Hội đồng hoạt động đã bỏ phiếu đầy đủ VÀ Tỷ lệ đồng ý (tán thành) đạt từ **50% trở lên**.

---

### 1.4. Tạm ngưng
*   **Giá trị CSDL**: `status = 'suspended'`
*   **Ý nghĩa**: Tạm thời đóng băng hoạt động phát hành bộ truyện (ví dụ tác giả bị bệnh...).
*   **Ràng buộc**: Hệ thống tự động ẩn nút tạo chapter mới của Mangaka và ẩn các công việc đang giao cho trợ lý.
*   **Kích hoạt**: Khi Hội đồng (Board) cập nhật trạng thái bộ truyện sang "Tạm ngưng" [SeriesController::updateStatus()].

---

### 1.5. Hoàn thành
*   **Giá trị CSDL**: `status = 'completed'`
*   **Ý nghĩa**: Dự án truyện Manga kết thúc tốt đẹp sau khi phát hành toàn bộ các chương. Khóa cứng mọi chỉnh sửa đối với toàn bộ dữ liệu của series.
*   **Kích hoạt**: Khi Hội đồng (Board) đánh dấu Hoàn thành bộ truyện [SeriesController::updateStatus()].
    *   *Chốt chặn Backend:* Hệ thống từ chối lưu trạng thái này nếu phát hiện còn bất kỳ chapter nào chưa được duyệt xong VÀ bắt buộc phải có ít nhất 1 chapter được phê duyệt là **Chương cuối (`is_final = 1`)**.

---

### 1.6. Từ chối / Đã hủy
*   **Giá trị CSDL**: `status = 'canceled'`
*   **Ý nghĩa**: Đề xuất bộ truyện bị Board bác bỏ (nếu đang chờ duyệt) hoặc bộ truyện đang chạy bị đình bản vĩnh viễn (do thứ hạng/điểm bình chọn độc giả quá thấp).
*   **Kích hoạt**: 
    *   *Từ chối:* Khi Board chốt từ chối đề xuất mới (chuyển sang `canceled` khi chưa gán editor_id).
    *   *Đã hủy:* Khi Board chốt hủy phát hành bộ truyện đang chạy [SeriesController::updateStatus()].

---
---

## 🎞️ 2. CHAPTER & PAGE (CHƯƠNG TRUYỆN & TRANG VẼ)
Quản lý quy trình sáng tác phân cảnh và vẽ chi tiết của Studio.
*   **Bảng dữ liệu:** `chapters` và `pages`
*   **Trường trạng thái trong CSDL:** `status`

---

### 2.1. Phác thảo Kịch bản (Storyboard)
*   **Giá trị CSDL**: `status = 'drafting'`
*   **Ý nghĩa**: Giai đoạn Mangaka lên kịch bản phân cảnh thô. Đây là không gian làm việc riêng tư của tác giả trước khi chốt hướng đi.
*   **Ràng buộc (Task Gating & Notification Suppression)**: 
    *   Mọi Task giao cho Trợ lý ở giai đoạn này đều ẩn hoàn toàn để tránh Trợ lý vẽ nhầm kịch bản thô gây lãng phí chi phí.
    *   **Ngăn chặn thông báo sớm**: Nếu Mangaka cập nhật trạng thái của Trang (Page) sang `drawing` khi Chapter chứa trang đó vẫn đang ở trạng thái `drafting` hoặc `reviewing_draft`, hệ thống sẽ **ngăn chặn không gửi thông báo giao việc** cho Trợ lý (chặn tại `PageController::update()`). Các thông báo này chỉ được kích hoạt đồng loạt khi kịch bản được Editor duyệt thông qua chính thức (Chapter chuyển sang `drawing`).
*   **Kích hoạt**: Khi Mangaka tạo mới Chapter [ChapterController::store()] (Hệ thống mặc định gán `drafting`).

---

### 2.2. Chờ duyệt Kịch bản
*   **Giá trị CSDL**: `status = 'reviewing_draft'`
*   **Ý nghĩa**: Bản phác thảo kịch bản (Storyboard) được nộp lên cho Biên tập viên chuyên trách thẩm định về cốt truyện và lời thoại.
*   **Ràng buộc**: Khóa chỉnh sửa dữ liệu của chương khi đang chờ duyệt.
*   **Kích hoạt**: Khi Mangaka bấm nút **"Nộp duyệt Bản nháp"** [SubmissionController::store() với type='draft'].

---

### 2.3. Đang vẽ Chi tiết
*   **Giá trị CSDL**: `status = 'drawing'`
*   **Ý nghĩa**: Kịch bản đã thông qua. Hệ thống tự động mở khóa hiển thị toàn bộ Task trên màn hình của Assistant và gửi thông báo để trợ lý tiến hành vẽ chi tiết.
*   **Kích hoạt**: Khi Editor bấm **"Phê duyệt"** kịch bản nháp [ReviewController::store() trạng thái 'approved' khi chapter ở 'reviewing_draft'].

---

### 2.4. Chờ duyệt Bản vẽ
*   **Giá trị CSDL**: `status = 'reviewing_final'`
*   **Ý nghĩa**: Mangaka gom toàn bộ tranh vẽ của trợ lý, vẽ hoàn thiện trang truyện cuối cùng và gửi bản thảo hoàn chỉnh (Manuscript) dưới dạng file nén `.zip` hoặc file `.pdf` lên Editor duyệt.
*   **Ràng buộc**: Khóa cứng toàn bộ dữ liệu của chapter, cấm Mangaka và trợ lý thay đổi nội dung.
*   **Kích hoạt**: Khi Mangaka bấm nút **"Nộp duyệt Bản hoàn chỉnh"** [SubmissionController::store() với type='final'].
    *   *Điều kiện chốt:* Toàn bộ Task phụ của trợ lý thuộc chapter này phải ở trạng thái `completed`.

---

### 2.5. Đã duyệt phát hành
*   **Giá trị CSDL**: `status = 'approved'`
*   **Ý nghĩa**: Chương truyện đạt chuẩn xuất bản của tòa soạn. Khóa chỉnh sửa vĩnh viễn (chỉ Editor phụ trách mới có quyền mở khóa trả về `drawing` để tác giả sửa nếu phát hiện lỗi sau đó).
*   **Kích hoạt**: Khi Editor chốt **"Phê duyệt"** bản thảo hoàn chỉnh [ReviewController::store() trạng thái 'approved' khi chapter ở 'reviewing_final'].

---

### 2.6. Đã xuất bản
*   **Giá trị CSDL**: `status = 'published'`
*   **Ý nghĩa**: Chương truyện chính thức phát hành thương mại ra công chúng. Cho phép độc giả bình chọn để tính điểm xếp hạng định kỳ.
*   **Kích hoạt**: Khi Editorial Board bấm nút xuất bản chương truyện [ChapterController::publish()].

---

### 🔄 Luồng từ chối của Biên tập viên (Rejection Workflow)
Khi Editor phát hiện lỗi trên bản thảo và bấm **"Từ chối"** [ReviewController::store() với status='rejected']:
*   Nếu từ chối duyệt kịch bản: Chapter từ `reviewing_draft` $\rightarrow$ trả về **`drafting`** để tác giả sửa cốt truyện.
*   Nếu từ chối duyệt bản vẽ hoàn chỉnh: Chapter từ `reviewing_final` $\rightarrow$ trả về **`drawing`** để tác giả/trợ lý sửa tranh lỗi theo nét vẽ ghi chú đỏ (Annotations).

---
---

## 🛠️ 3. TASK (CÔNG VIỆC CỦA TRỢ LÝ)
Quản lý các phần việc vẽ chuyên biệt do Mangaka phân công cho trợ lý (Assistant) trên từng vùng trang truyện.
*   **Bảng dữ liệu:** `tasks`
*   **Trường trạng thái trong CSDL:** `status`

---

### 3.1. Chờ xử lý
*   **Giá trị CSDL**: `status = 'pending'`
*   **Ý nghĩa**: Công việc mới được tạo và giao cho Assistant. Assistant chưa bắt tay vào thực hiện.
*   **Kích hoạt**: Khi Mangaka khoanh phân vùng và bấm nút **"Giao việc"** [TaskController::store()].

---

### 3.2. Đang làm
*   **Giá trị CSDL**: `status = 'in_progress'`
*   **Ý nghĩa**: Assistant đã bấm nhận việc để khóa task (không cho Mangaka gán người khác), hoặc đang chỉnh sửa lại theo yêu cầu sửa đổi của Mangaka.
*   **Kích hoạt**: 
    *   Assistant bấm nút **"Nhận việc"** tại trang danh sách task [TaskController::updateStatus()].
    *   Mangaka bấm từ chối sản phẩm nộp của Assistant.

---

### 3.3. Hoàn thành
*   **Giá trị CSDL**: `status = 'completed'`
*   **Ý nghĩa**: Bài vẽ đạt yêu cầu và được Mangaka phê duyệt. Khóa cứng trạng thái của task, hệ thống tự động tính thù lao (300.000 VNĐ) cho Assistant.
*   **Kích hoạt**: Khi Mangaka bấm **"Phê duyệt"** sản phẩm nộp của Assistant [ReviewController::store() trạng thái 'approved'].

---
---

## 📥 4. SUBMISSION (BẢN THẢO/BẢN VẼ NỘP)
Quản lý file vật lý (ảnh vẽ, file ZIP) do Assistant nộp lên (nộp sản phẩm vẽ) hoặc Mangaka nộp lên (nộp bản thảo chương).
*   **Bảng dữ liệu:** `submissions`
*   **Trường trạng thái trong CSDL:** `status`

---

### 4.1. Chờ duyệt
*   **Giá trị CSDL**: `status = 'pending'`
*   **Ý nghĩa**: File vẽ/Bản thảo vừa được tải lên, đang nằm trong hàng đợi kiểm duyệt của Mangaka (đối với Task) hoặc Editor (đối với Chapter).
*   **Kích hoạt**: 
    *   Assistant bấm **"Nộp bài vẽ"** [SubmissionController::store() với taskId].
    *   Mangaka bấm **"Nộp Chapter"** [SubmissionController::store() với chapterId].

---

### 4.2. Đang đánh giá
*   **Giá trị CSDL**: `status = 'reviewed'`
*   **Ý nghĩa**: Người duyệt (Editor hoặc Mangaka) bắt đầu viết nhận xét, ghi chú sửa đổi nhưng chưa chốt quyết định cuối cùng.
*   **Kích hoạt**: Hệ thống tự động chuyển đổi khi người duyệt bắt đầu tương tác lưu nháp nhận xét.

---

### 4.3. Đã duyệt
*   **Giá trị CSDL**: `status = 'approved'`
*   **Ý nghĩa**: File nộp đạt chất lượng, được phê duyệt thông qua.
*   **Kích hoạt**: Người duyệt chốt quyết định **"Phê duyệt"** [ReviewController::store() trạng thái 'approved'].

---

### 4.4. Từ chối
*   **Giá trị CSDL**: `status = 'rejected'`
*   **Ý nghĩa**: Bản thảo/bản vẽ bị trả lại yêu cầu vẽ lại.
*   **Kích hoạt**: Người duyệt chốt quyết định **"Từ chối"** [ReviewController::store() trạng thái 'rejected'].
