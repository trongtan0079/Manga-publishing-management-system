Tài Liệu Đặc Tả Hệ Thống
Sơ lược :
ĐỀ TÀI : "Hệ thống quản lý quy trình sáng tác và xuất bản Manga
Manga Creation Workflow and Publishing Management System"	
 Trong ngành công nghiệp Manga, quá trình từ lúc sáng tác đến khi xuất bản đòi hỏi sự phối hợp chặt chẽ giữa nhiều bên: tác giả, trợ lý, biên tập viên và hội đồng biên tập. Hệ thống hỗ trợ quản lý toàn bộ quy trình này, từ nộp bản thảo, phân công công việc nội bộ studio, đến dữ liệu bình chọn và ra quyết định xuất bản.	"
 - Tác giả và trợ lý phải dùng nhiều ứng dụng khác nhau để trao đổi công việc, dễ nhầm lẫn và khó kiểm soát tiến độ từng trang, từng khung hình.
- Biên tập viên và hội đồng không có công cụ chung để theo dõi xem studio đang làm đến đâu, dẫn đến chậm deadline và thiếu thông tin khi ra quyết định."	
"Mangaka
Assistant 
Tantou Editor
Editorial Board"	
"Mangaka

- Tạo hồ sơ giới thiệu series mới và nộp bản thảo sơ bộ để trình lên hội đồng xét duyệt
- Chọn từng vùng trên trang truyện và giao việc cụ thể cho từng trợ lý (vẽ nền, tô bóng, hiệu ứng…)
- Xem bản tổng hợp sau khi trợ lý hoàn thành, phê duyệt hoặc yêu cầu chỉnh sửa ngay trên trang
- Theo dõi thứ hạng của series mình trên bảng xếp hạng và nhận thông báo khi series có nguy cơ bị huỷ

Assistant
- Xem danh sách công việc được giao, tải file trang truyện cần xử lý cùng các tài nguyên hỗ trợ
- Hoàn thiện phần việc được giao và gửi lại kết quả cho tác giả kiểm duyệt
- Theo dõi số trang đã được duyệt và thu nhập tương ứng theo từng tháng

Tantou Editor
- Xem bản thảo và đánh dấu trực tiếp lên trang những chỗ cần chỉnh sửa nội dung, thoại, kịch bản
- Quản lý hồ sơ và số liệu để bảo vệ series trước hội đồng biên tập
- Theo dõi tiến độ hoàn thiện của studio theo thời gian thực để đảm bảo kịp deadline giao bản in

Editorial Board
- Bỏ phiếu thông qua series mới và quyết định lịch xuất bản (hàng tuần hoặc hàng tháng)
- Ra quyết định huỷ series đang xếp hạng thấp hoặc thay đổi hình thức xuất bản dựa trên kết quả thực tế
- Nhập dữ liệu bình chọn từ độc giả vào hệ thống sau mỗi kỳ phát hành
- Xem bảng xếp hạng các series được tổng hợp sau mỗi lần nhập dữ liệu

*Công cụ phân vùng bản vẽ:*
- Hỗ trợ bộ công cụ vẽ tay thủ công chuyên nghiệp để tự tay phân chia khung hình, ô thoại, nhân vật, bối cảnh trên trang truyện một cách trực quan và chính xác tuyệt đối.
- Giúp Mangaka dễ dàng chỉ định vùng làm việc cụ thể khi giao Task cho các trợ lý (Assistant).



1.4 Đối tượng sử dụng
1.4.1 Admin  
Admin là người quản trị hệ thống, chịu trách nhiệm quản lý người dùng, phân quyền truy cập và đảm bảo hệ thống hoạt động ổn định. Admin có quyền kiểm soát các chức năng quản trị và theo dõi hoạt động chung của hệ thống.

- Quản lý tài khoản người dùng (Tìm kiếm & Phân trang phía Máy chủ - Server-side pagination)
- Quản lý vai trò và phân quyền
- Theo dõi hoạt động hệ thống (Nhật ký hoạt động Audit Trail lưu vết thao tác, IP, thời gian)
- Sao lưu dữ liệu dự phòng (Live Database Backup - SQL dump 1-click)
- Xem thông báo hệ thống và báo cáo thống kê tổng hợp (biểu đồ trực quan)

1.4.2 Mangaka  
Mangaka là tác giả chính của tác phẩm Manga, chịu trách nhiệm xây dựng nội dung, quản lý quá trình sáng tác và phối hợp với các Assistant để hoàn thiện tác phẩm trước khi gửi kiểm duyệt.

- Tạo hồ sơ giới thiệu Series mới và nộp bản thảo sơ bộ để trình lên Hội đồng xét duyệt
- Quản lý thông tin Series và Chapter
- Phân chia công việc trên từng trang Manga và giao việc cho Assistant
- Theo dõi tiến độ thực hiện công việc của Assistant
- Kiểm duyệt kết quả công việc do Assistant gửi lên
- Chỉnh sửa và hoàn thiện nội dung Chapter theo phản hồi từ Tantou Editor
- Theo dõi kết quả bình chọn và thứ hạng của Series
- Theo dõi các thông báo liên quan đến tình trạng xuất bản của Series

1.4.3 Assistant  
Assistant là người hỗ trợ Mangaka thực hiện các công việc được phân công trong quá trình hoàn thiện Manga. Các công việc có thể bao gồm xử lý artwork, chỉnh sửa nội dung hoặc các nhiệm vụ khác do Mangaka giao.

- Xem danh sách công việc được giao
- Tải trang Manga và các tài nguyên liên quan phục vụ công việc
- Thực hiện các nhiệm vụ được phân công (vẽ nền, tô màu, chỉnh sửa hình ảnh, bổ sung chi tiết, …)
- Nộp Submission sau khi hoàn thành công việc
- Theo dõi trạng thái Task và Submission
- Tiếp nhận phản hồi từ Mangaka hoặc Tantou Editor
- Theo dõi số trang đã được duyệt
- Theo dõi các thông báo liên quan đến công việc được giao

1.4.4 Tantou Editor  
Tantou Editor là biên tập viên trực tiếp theo dõi quá trình phát triển của các Series Manga. Vai trò này chịu trách nhiệm kiểm duyệt nội dung, đưa ra nhận xét và hỗ trợ Mangaka nâng cao chất lượng tác phẩm trước khi xuất bản.

- Review bản thảo Series và Chapter
- Đưa ra nhận xét và đề xuất chỉnh sửa nội dung
- Theo dõi tiến độ thực hiện và thời hạn (Deadline) của Series
- Quản lý hồ sơ Series trong quá trình kiểm duyệt
- Theo dõi các thông báo liên quan đến quá trình kiểm duyệt

1.4.5 Editorial Board  
Editorial Board là hội đồng biên tập chịu trách nhiệm đánh giá hiệu quả hoạt động của các Series Manga dựa trên dữ liệu bình chọn và kết quả phát hành. Hội đồng đưa ra các quyết định liên quan đến việc tiếp tục xuất bản, thay đổi lịch phát hành hoặc ngừng phát hành Series.

- Đánh giá hồ sơ giới thiệu Series mới
- Bỏ phiếu xét duyệt các Series trước khi phát hành và gán Biên tập viên chuyên trách (Tantou Editor) cho bộ truyện
- Nhập dữ liệu bình chọn của độc giả
- Theo dõi bảng xếp hạng các Series
- Xem báo cáo thống kê kết quả phát hành
- Đưa ra quyết định tiếp tục xuất bản hoặc ngừng phát hành Series
- Theo dõi các thông báo liên quan đến hoạt động xuất bản

---

## 1.5 Quy trình Quản lý Trạng thái Chương truyện (Chapter Lifecycles & Workflows)

Hệ thống áp dụng cơ chế quản lý vòng đời chặt chẽ đối với các Chương truyện (Chapters) để tối ưu hóa hiệu suất cộng tác giữa Họa sĩ chính (Mangaka) và Trợ lý (Assistant), đồng thời đảm bảo tính bảo mật và kiểm duyệt của Tòa soạn:

### 1.5.1 Trạng thái khởi tạo (Tạo mới Chapter)
- Khi Mangaka tạo mới một chương truyện, hệ thống chỉ cho phép khởi tạo dưới 2 trạng thái: **Bản nháp (Drafting)** hoặc **Đang vẽ (Drawing)**. 
- Trạng thái **Đang chờ duyệt (Reviewing)** bị cấm sử dụng ở khâu tạo mới để ngăn chặn việc gửi một chương rỗng chưa có nội dung vẽ lên Biên tập viên.

### 1.5.2 Vận hành phân quyền nhiệm vụ (Tasks & Notifications Visibility)
- **Giai đoạn Bản nháp (Drafting)**:
  - Đây là không gian lập kế hoạch chuyên tư của Mangaka. Mangaka có thể tạo các phân cảnh, trang truyện và giao việc (Tasks) thử nghiệm cho Trợ lý.
  - Các công việc này tạm thời **ẩn hoàn toàn** đối với Trợ lý (Assistant không nhìn thấy trên dashboard công việc) và hệ thống **không gửi thông báo** giao việc, tránh gây nhiễu thông tin.
  - Trợ lý **không được phép nộp bản thảo/bản vẽ** cho các task thuộc chapter nháp hoặc thuộc bộ truyện chưa phê duyệt (đang ở trạng thái `planning`).
- **Giai đoạn Đang vẽ (Drawing)**:
  - Khi Mangaka chỉnh sửa chương và nâng trạng thái từ *Bản nháp (Drafting)* sang *Đang vẽ (Drawing)*, hệ thống sẽ chính thức **kích hoạt hiển thị công khai** tất cả các công việc đã phân công cho Trợ lý.
  - Đồng thời, một loạt **thông báo tự động sẽ gửi đến các Trợ lý** tương ứng để báo hiệu bắt đầu làm việc.
- **Giai đoạn Đang chờ duyệt (Reviewing)**:
  - Khi toàn bộ trang vẽ hoàn thành, Mangaka nộp bản thảo và chuyển trạng thái chương sang *Đang chờ duyệt (Reviewing)* để gửi tới Biên tập viên (Tantou Editor) đánh giá chất lượng.
- **Giai đoạn Đã duyệt (Approved) & Đã xuất bản (Published)**:
  - Biên tập viên phê duyệt đưa chương truyện vào trạng thái sẵn sàng phát hành hoặc xuất bản thương mại. Các trạng thái này là cuối cùng và bị khóa chỉnh sửa.
- **Ràng buộc khóa trạng thái hoàn thành (Completed Lock):**
  - Một khi công việc (Task) đã hoàn thành và được Mangaka duyệt (`status = 'completed'`), Trợ lý **bị chặn quyền thay đổi** trạng thái ngược trở lại `pending` hoặc `in_progress` để tránh làm sai lệch dữ liệu tiến độ và doanh thu thù lao.

---

## 1.6 Quy trình Kiểm duyệt và Giám sát vòng đời Đề án bộ truyện (Series Lifecycles & Operations)

Hệ thống áp dụng cơ chế quản lý và kiểm duyệt vòng đời vĩ mô chặt chẽ đối với các Bộ truyện (Series), phân định rõ ràng quyền hạn giữa Tác giả (Mangaka), Trợ lý (Assistant) và Hội đồng Biên tập (Editorial Board):

### 1.6.1 Giai đoạn Đề xuất Bộ truyện mới (Series Proposal & Draft)
- **Khởi tạo Bản nháp riêng tư (Draft Stage):**
  - Khi Mangaka tạo mới một bộ truyện (Series), hệ thống sẽ mặc định gán thuộc tính `publish_type = 'draft'` và đặt trạng thái là **Nháp (Chưa nộp)**.
  - Bộ truyện ở trạng thái này là không gian riêng tư của tác giả để phác thảo ý tưởng, viết tóm tắt cốt truyện và tải lên ảnh bìa. 
  - Hội đồng Biên tập (Editorial Board) và Biên tập viên (Tantou Editor) **không thể nhìn thấy** bản nháp này trên Dashboard của họ. Đồng thời, hệ thống chặn quyền xem chi tiết trực tiếp bằng đường dẫn (URL Bypass) của các vai trò này để bảo mật tuyệt đối cho tác giả.
- **Nộp Đề xuất (Submit Proposal):**
  - Khi tác giả đã hoàn thiện thông tin, tác giả bấm nút **Nộp Đề Xuất** (Submit Proposal). Trạng thái bộ truyện sẽ được chuyển sang dạng chờ duyệt (`publish_type = 'submitted'`).
  - Lúc này, bộ truyện mới chính thức hiển thị trên bảng chờ phê duyệt của Hội đồng Biên tập.

### 1.6.2 Phân quyền Tạo Chapter & Phân công trong giai đoạn Chờ duyệt
Để tạo điều kiện cho tác giả chuẩn bị trước bản thảo mẫu (Buffer Chapters) nhưng vẫn bảo mật quy trình sáng tác:
- **Tác giả chủ động chuẩn bị trước:** Trong khi bộ truyện đang chờ duyệt (trạng thái `planning`), tác giả Mangaka vẫn có quyền tạo trước các chapter nháp, tải lên các trang vẽ và phân công task vẽ cho các trợ lý.
- **Tầng bảo mật đối với Trợ lý (Assistant):** 
  - Trợ lý sẽ **không thể nhìn thấy** bất kỳ chapter hay task nào của bộ truyện khi bộ truyện chưa được Hội đồng Biên tập phê duyệt (ở trạng thái `planning`).
  - Hệ thống lọc bỏ hoàn toàn các task này khỏi bảng điều khiển của Trợ lý, đảm bảo họ không bắt tay vào làm việc trước khi dự án được cấp phép chính thức.
- **Tầng bảo mật đối với Hội đồng (Board) và Biên tập viên (Editor):**
  - Khi xem chi tiết bộ truyện đang chờ duyệt, mục **Danh sách Chapter** sẽ bị ẩn hoàn toàn đối với Hội đồng và BTV để họ tập trung đánh giá hồ sơ ý tưởng gốc thay vì bản thảo dang dở.

### 1.6.3 Phê duyệt và Giám sát hoạt động của Hội đồng Biên tập
Giao diện quản lý của Hội đồng Biên tập được phân tách thành 2 bảng giám sát độc lập để tối ưu hóa vận hành:
- **Bảng 1: Đề xuất bộ truyện mới (Chờ phê duyệt):**
  - Chỉ hiển thị các bộ truyện đã nộp ở trạng thái `planning`.
  - **Cơ chế Bỏ phiếu Xét duyệt Đề xuất:** 
    - Mỗi thành viên Hội đồng Biên tập (Editorial Board) khi đăng nhập sẽ thực hiện bỏ phiếu **Đồng ý (Approve)** hoặc **Từ chối (Reject)** cho từng đề xuất mới.
    - Tỉ lệ tán thành được tính toán tự động và liên tục dựa trên số lượng phiếu đồng ý chia cho tổng số thành viên hội đồng có trạng thái hoạt động (`active`) trong hệ thống.
    - **Chốt chặn Ngưỡng phê duyệt (>= 50%):** Hệ thống chỉ cho phép mở khóa tùy chọn "Thông qua (Phê duyệt)" để chuyển trạng thái bộ truyện sang **Đang triển khai (Ongoing)** khi đề xuất đạt tỉ lệ tán thành từ **50% trở lên** (tương đương tối thiểu 3/5 phiếu nếu hội đồng có 5 người). Các trường hợp dưới 50% chỉ được quyền bỏ phiếu tiếp hoặc chọn quyết định **Từ chối (Hủy dự án - Canceled)**.
    - Khi thông qua đề xuất hợp lệ, hội đồng tiến hành thiết lập Lịch phát hành (Hàng tuần/Hàng tháng) và bắt buộc gán Biên tập viên chuyên trách (Tantou Editor) phụ trách bộ truyện.
- **Bảng 2: Bộ truyện đang hoạt động (Giám sát & Quản lý):**
  - Hiển thị các bộ truyện đang trong vòng đời sản xuất gồm **Đang triển khai (Ongoing)** và **Tạm ngưng (Suspended)**.
  - Hội đồng Biên tập có quyền cập nhật linh hoạt các trạng thái và thay đổi/gán lại Biên tập viên phụ trách bộ truyện (Tantou Editor).
  - Các quyền cập nhật trạng thái gồm:
    - **Tạm ngưng (Suspended):** Khi bộ truyện cần tạm dừng phát hành (treo bút). Lúc này, nút tạo chapter mới của tác giả sẽ tự động ẩn đi, các task của trợ lý cũng bị ẩn tạm thời. Hội đồng có thể khôi phục lại trạng thái `ongoing` bất kỳ lúc nào để tác giả tiếp tục vẽ.
    - **Hoàn thành (Completed):** Đóng dự án khi tác phẩm kết thúc tốt đẹp.
    - **Đã hủy (Canceled):** Đình bản hoặc khai tử vĩnh viễn bộ truyện. Khi chuyển sang trạng thái này, bộ truyện sẽ biến mất khỏi hàng đợi giám sát của Hội đồng.
  - **Khóa an toàn khi Hoàn thành (Completed Lock):** Hệ thống chặn đứng hành vi chuyển trạng thái bộ truyện sang *Hoàn thành (Completed)* nếu phát hiện còn bất kỳ chương truyện (Chapter) nào chưa được Biên tập viên duyệt xong (đang ở trạng thái nháp, đang vẽ hoặc đang chờ duyệt) VÀ bắt buộc phải có ít nhất một chương được phê duyệt là **Chương cuối (End Chapter)**.
  - **Giám sát Tiến độ Chapter và Đóng truyện:** 
    - Bảng 2 hiển thị tỉ lệ `Chương đã duyệt hoàn tất / Tổng số chương hiện có` giúp Hội đồng giám sát tiến độ thực tế.
    - Hệ thống áp dụng cơ chế **Chương cuối (End Chapter)**: Tác giả có thể đánh dấu một chapter là chương cuối (tối đa 1 chương cuối mỗi bộ truyện). Cột tiến độ sẽ chỉ hiển thị nhãn xanh **Hoàn tất** khi chương cuối này đã được Editor duyệt thành công. Nếu không, nhãn hiển thị sẽ luôn giữ ở dạng **Đang làm** để tránh Hội đồng kết thúc nhầm bộ truyện khi tác giả vẫn đang sáng tác.
    - Khi chương cuối được duyệt, hệ thống tự động gửi thông báo đến toàn bộ các thành viên Hội đồng Biên tập để họ kịp thời kiểm tra và chuyển trạng thái Series sang **Hoàn thành**.

### 1.6.4 Hệ thống Cảnh báo tự động và Ràng buộc Xếp hạng (Rankings)
- **Tự động gửi thông báo:** Khi Hội đồng Biên tập thay đổi trạng thái hoặc chu kỳ phát hành của bộ truyện, hệ thống tự động gửi thông báo loại `series_warning` cho tác giả tương ứng.
- **Cảnh báo xếp hạng thấp:** Khi Hội đồng Biên tập nhập dữ liệu bình chọn định kỳ, nếu phát hiện điểm số dưới 50 hoặc thứ hạng rơi xuống từ hạng 5 trở đi, hệ thống sẽ tự động gửi cảnh báo khẩn cấp `series_warning` tới tác giả để họ chủ động điều chỉnh kịch bản.
- **Khóa bảo vệ chấm điểm (Ranking Safeguards):** Hệ thống cấm hoàn toàn việc tạo mới hoặc chỉnh sửa điểm xếp hạng cho các bộ truyện đang ở trạng thái nháp, chờ duyệt (`planning`) hoặc đã hủy (`canceled`) ở cả tầng giao diện lẫn kiểm tra dữ liệu phía máy chủ (Backend Validation).

---

## 1.7 Công cụ Đánh dấu lỗi Trực quan của Biên tập viên (Tantou Editor Visual Annotations)

Để tối ưu hóa quá trình duyệt bản thảo giữa Biên tập viên chuyên trách (Tantou Editor) và Tác giả (Mangaka):
- **Cơ chế Vẽ khoanh vùng báo lỗi (Visual Annotation Canvas):**
  - Tại trang chi tiết bản thảo (`submission_detail`), hệ thống hiển thị danh sách các trang truyện hoàn thiện thuộc chương.
  - Editor có thể nhấn nút **Đánh dấu lỗi** trên từng trang để mở giao diện Modal Canvas vẽ. Editor nhấp và kéo chuột để vẽ một khung chữ nhật màu đỏ bao quanh vùng bị lỗi trên ảnh, nhập nội dung phản hồi và lưu qua API không cần tải lại trang.
- **Hệ tọa độ chuẩn hóa tương thích (Responsive Scaling System):**
  - Để các ô khoanh vùng hiển thị chính xác trên mọi thiết bị và độ phân giải màn hình (Responsive), hệ thống sử dụng cơ chế ánh xạ tọa độ chuẩn hóa về kích thước ảo cố định là **800 x 1000 pixels**.
  - Khi lưu: Tọa độ thực tế vẽ trên trình duyệt được chia cho kích thước hiển thị hiện tại của ảnh và nhân ngược với 800 (cho chiều ngang) và 1000 (cho chiều dọc).
  - Khi hiển thị cho Mangaka: Tọa độ lưu trong CSDL được quy đổi thành tỷ lệ phần trăm `%` và vẽ đè (absolute position overlay) trực quan lên ảnh.
- **Giao diện phản hồi của Tác giả (Mangaka Feedback Screen):**
  - Tại màn hình chi tiết trang truyện của Mangaka (`page_detail`), các vùng lỗi được viền khung đứt nét màu đỏ nổi bật.
  - Khi di chuột vào vùng khoanh đỏ, hệ thống kích hoạt Bootstrap Popover hiển thị chi tiết lỗi cần sửa và tên Editor đã đánh dấu. Đồng thời cột bên phải liệt kê danh sách tổng hợp lỗi để tác giả dễ dàng theo dõi sửa chữa.
- **Ràng buộc Khóa an toàn và Phân quyền:**
  - Khi chương truyện đã được phê duyệt (`approved`) hoặc xuất bản (`published`), hệ thống sẽ khóa cứng các API lưu/xóa ghi chú lỗi để tránh làm thay đổi lịch sử duyệt bản thảo.
  - Chỉ những bản thảo ở trạng thái **Chờ duyệt (Pending)** mới được phép đánh giá, chặn hành vi đánh giá lại các bản thảo cũ.
  - API lấy danh sách ghi chú lỗi (`get_annotations`) được xác thực phân quyền nghiêm ngặt ở backend, chỉ cho phép Admin, Board, Editor phụ trách, Mangaka tác giả và Assistant liên đới truy xuất.
  - Tọa độ ghi chú lỗi gửi lên bắt buộc phải hợp lệ trong khung ảo $800 \times 1000$ pixels.