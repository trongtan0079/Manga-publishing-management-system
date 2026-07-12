# Hướng dẫn sử dụng: Hệ thống Quản lý Quy trình Xuất bản Manga (Manga Publishing Workflow Management System)

Tài liệu này hướng dẫn chi tiết các luồng nghiệp vụ của cả **5 vai trò (Roles)** trong hệ thống giúp người dùng dễ dàng thao tác và làm quen với quy trình xuất bản manga chuẩn chuyên nghiệp.

---

## 🔑 1. Đăng Nhập & Màn hình chính (Dashboard)
1. Truy cập vào hệ thống qua trình duyệt.
2. Nhập thông tin tài khoản đăng nhập (Username & Password).
3. Sau khi đăng nhập thành công, hệ thống sẽ tự động chuyển hướng bạn đến **Dashboard dành riêng cho vai trò của bạn** với các số liệu thống kê trực quan và danh sách thông báo nghiệp vụ thời gian thực.
4. **Nhấp vào thông báo:** Bất cứ khi nào nhận được thông báo mới (ở Dashboard hoặc Dropdown thanh Navbar), bạn chỉ cần **nhấp trực tiếp vào thông báo** đó để hệ thống tự động đưa bạn đến đúng trang chi tiết tài nguyên cần xử lý (ví dụ: trang nộp bài, trang tạo đánh giá, chi tiết bộ truyện...).

---

## 👑 2. Phân hệ Admin (Quản lý Hệ thống)
* **Xem danh sách người dùng:** Truy cập menu `Quản lý Users` trên thanh bên (Sidebar).
* **Thêm người dùng mới:** Nhấn nút `Thêm User mới`, nhập đầy đủ thông tin (Họ tên, Email, Tên đăng nhập, Mật khẩu) và chọn vai trò thích hợp (`mangaka`, `assistant`, `editor`, `board`, `admin`).
* **Khóa/Kích hoạt tài khoản:** Có thể thay đổi trạng thái hoạt động của tài khoản thành `active` hoặc `inactive`.

---

## 🎨 3. Phân hệ Mangaka (Họa sĩ chính)
Quy trình làm việc chuẩn của Mangaka bao gồm:
1. **Đăng ký Series mới:** Truy cập `Danh sách Series` -> nhấn `Đăng ký Series mới`. Nhập tiêu đề, mô tả, tải lên ảnh bìa (Cover Image) và **Tài liệu đề xuất/Bản thảo sơ bộ (Proposal PDF/ZIP)** để gửi lên Hội đồng Biên tập phê duyệt.
2. **Quản lý Chapter:** Khi Series được duyệt sang trạng thái `Đang triển khai`, vào chi tiết bộ truyện và nhấn `Thêm Chapter mới`.
3. **Quản lý Trang truyện (Pages):** Nhấp vào Chapter mới tạo -> Chọn `Thêm Trang vẽ` -> Tải lên bản phác thảo bố cục (Layout Sketch) cho từng trang.
4. **Giao việc cho Trợ lý (Assistant):** 
   - Trên giao diện chi tiết trang vẽ, nhấn `Giao việc mới (Assign Task)`.
   - Chọn trợ lý cụ thể, phân vùng lỗi cần xử lý (Page Region), chọn loại công việc (Vẽ nền, Đi nét, Tô màu, Hiệu ứng chữ) và nhập thời hạn hoàn thành.
5. **Theo dõi tiến độ thời gian thực (Real-time Progress):**
   - Vào chi tiết Chapter hoặc Series để xem thanh tiến độ hoàn thiện của studio (`Tiến độ Studio`).
   - Nếu chương truyện tự vẽ không thuê trợ lý, hệ thống sẽ hiển thị nhãn `Tác giả tự vẽ` (tiến độ 100%).
   - Xem ma trận ô màu tiến độ (Grid) để nắm khâu nào trợ lý đã làm xong (Màu xanh) và khâu nào đang làm (Màu vàng).
6. **Đánh giá & Duyệt bài trợ lý:** Khi trợ lý nộp sản phẩm, nhấp vào thông báo để mở chi tiết bản thảo, xem so sánh ảnh trước/sau và nhấn `Đánh giá & Phê duyệt` để xác nhận hoàn thành Task.
7. **Nộp Chapter hoàn chỉnh:** Khi tiến độ chương đạt 100%, nhấn nút `Nộp Chapter` để nộp toàn bộ file bản thảo hoàn thiện gửi lên cho Biên tập viên kiểm duyệt.

---

## 🖌️ 4. Phân hệ Assistant (Trợ lý vẽ tranh)
1. **Nhận công việc:** Khi Mangaka giao việc, nhấp vào thông báo hoặc truy cập menu `Nhiệm vụ được giao` để xem chi tiết yêu cầu, tải về file vẽ nháp cùng các tài nguyên hỗ trợ (Resource URL).
2. **Thực hiện vẽ:** Xử lý đồ họa trên phần mềm chuyên dụng của bạn theo đúng mô tả.
3. **Nộp sản phẩm hoàn thành:** 
   - Vào chi tiết Task -> nhấn `Nộp bản vẽ (Upload Submission)`. Tải lên file ảnh kết quả và ghi chú mô tả quá trình thực hiện.
   - Trạng thái công việc sẽ tự động chuyển sang `Chờ duyệt` (pending). Bạn có thể xóa bản thảo này để nộp lại nếu tác giả chưa tiến hành review.
4. **Theo dõi thu nhập:** Xem bảng thống kê thu nhập theo số trang vẽ đã được tác giả phê duyệt thành công hàng tháng trực tiếp trên Dashboard.

---

## 📝 5. Phân hệ Tantou Editor (Biên tập viên chuyên trách)
1. **Nhận nhiệm vụ:** BTV được Hội đồng phân công phụ trách bộ truyện sẽ nhận thông báo. Bạn là người duy nhất có quyền kiểm duyệt và đánh dấu sửa lỗi trên bộ truyện này.
2. **Kiểm duyệt bản thảo & Vẽ khoanh vùng báo lỗi trực quan (Annotations):**
   - Khi Mangaka nộp Chapter, nhấp vào thông báo để mở màn hình chi tiết Bản thảo.
   - Nhấp vào nút `Đánh dấu lỗi` dưới mỗi trang vẽ để mở canvas vẽ lỗi trực quan.
   - **Nhấn giữ và kéo chuột** tạo khung đỏ trên bức vẽ, nhập nội dung ghi chú sửa đổi (Ví dụ: "Lỗi thoại nhân vật", "Thiếu bóng nền") rồi bấm `Lưu`. Mangaka sẽ thấy trực tiếp các chấm đỏ này trên trang vẽ của họ kèm nội dung chi tiết khi di chuột vào.
3. **Lưu đánh giá chính thức:** Nhấp vào `Chuyển sang Review` để ghi nhận xét tổng quan, chấm điểm chất lượng (1-10) và chọn quyết định phê duyệt: `Phê duyệt (Approved)` hoặc `Từ chối (Rejected)`.
4. **Hồ sơ bảo vệ tác phẩm (Dossier Defense):** 
   - Với những bộ truyện có thứ hạng thấp trên bảng xếp hạng định kỳ, Editor truy cập menu `Hồ sơ bảo vệ` bên thanh bên để xem biểu đồ xu hướng.
   - Nhập nội dung phân tích tiềm năng và lập luận giải trình vào mục `Ghi chú biện hộ (Dossier Notes)` để bảo vệ bộ truyện, thuyết phục Hội đồng không hủy ngang dự án.

---

## 🏛️ 6. Phân hệ Editorial Board (Hội đồng Biên tập / Ban Giám đốc)
Hội đồng nắm giữ quyền hành quản lý cao nhất về việc phát hành và thương mại tác phẩm:
1. **Bỏ phiếu và Thông qua bộ truyện mới (Publish Series):**
   - Vào menu `Duyệt & Quản lý Series`.
   - Xem danh sách đề xuất truyện mới của Mangaka và xem bản thảo đính kèm.
   - **Bỏ phiếu:** Mỗi thành viên Hội đồng đăng nhập thực hiện nhấn nút **👍 Đồng ý** hoặc **👎 Từ chối** dưới cột Tỉ lệ tán thành để thể hiện ý kiến của mình.
   - **Chốt quyết định:** 
     - Giao diện chốt quyết định sẽ bị khóa hoàn toàn và hiển thị cảnh báo **"Chờ đủ phiếu bầu (X/Y)"** cho đến khi **tất cả** thành viên Hội đồng đang hoạt động hoàn thành bỏ phiếu.
     - Khi đã đầy đủ phiếu bầu, form quyết định sẽ tự động hiển thị. Quyết định **Từ chối (Hủy dự án)** luôn khả dụng. Tuy nhiên, tùy chọn **Thông qua (Phê duyệt)** chỉ được mở khóa khi tỷ lệ tán thành đạt từ **50% trở lên**.
     - Chọn Biên tập viên chuyên trách gán phụ trách, chọn Lịch phát hành (`Hàng tuần` hoặc `Hàng tháng`) rồi bấm **Ghi nhận** để hoàn tất phê duyệt bộ truyện.
2. **Giám sát số liệu & Quyết định hủy bộ truyện xếp hạng thấp:**
   - Trên bảng `Bộ truyện đang phát hành`, xem nhanh thứ hạng tuần/tháng (`Hạng #...`) và điểm bình chọn mới nhất của từng bộ truyện.
   - Hệ thống tự động gắn nhãn cảnh báo đỏ `Có nguy cơ bị hủy` cho các bộ truyện xếp hạng kém (hạng >= 5 hoặc điểm < 50).
   - Trước khi quyết định đổi trạng thái thành `Đình bản (Đã hủy)` hoặc `Tạm ngưng`, Hội đồng hãy nhấp nút `Có Biện Hộ` màu đỏ để đọc giải trình từ Editor chuyên trách nhằm đưa ra quyết định khách quan nhất.
3. **Nhập dữ liệu bình chọn của độc giả sau kỳ phát hành:**
   - Vào menu `Quản lý Xếp hạng` -> Nhấn nút `Tạo Đánh giá Xếp hạng Mới`.
   - Chọn ngày bắt đầu kỳ phát hành (Kỳ tuần hoặc kỳ tháng).
   - Hệ thống tự động hiển thị lưới danh sách toàn bộ Manga đang hoạt động. Nhập nhanh số lượng phiếu bầu thu nhận từ độc giả vào cột số phiếu cho từng truyện và bấm `Tính Toán & Công Bố`.
   - **Tự động hóa:** Hệ thống tự động phân tích tìm ra truyện nhiều phiếu nhất gán điểm quy chuẩn `100.00 điểm`. Các truyện còn lại được tính điểm tỉ lệ và tự động đánh số thứ hạng `#1`, `#2`, `#3`... chèn hàng loạt vào Database. Các Mangaka của bộ truyện sẽ lập tức nhận thông báo xếp hạng và cảnh báo rủi ro tương ứng.
4. **Xem bảng xếp hạng & Tải báo cáo:**
   - Xem nhanh biểu đồ cột điểm số và danh sách Top 5 / Bottom 5 ngay tại Dashboard Board.
   - Nhấn nút `Tải Báo cáo Xếp Hạng (CSV)` để xuất file số liệu báo cáo xuất bản phục vụ các cuộc họp trực tiếp.
