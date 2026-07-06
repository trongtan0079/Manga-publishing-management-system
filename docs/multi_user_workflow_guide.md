# HƯỚNG DẪN LUỒNG NGHIỆP VỤ ĐA TÁC NHÂN (MULTI-USER WORKFLOW)
*Tài liệu thuyết minh cách hệ thống quản lý, phân quyền và xử lý luồng công việc khi có nhiều người dùng đồng thời*

---

## 1. Cơ Chế Cách Ly Dữ Liệu & Phân Quyền (Data Isolation)
Khi hệ thống có nhiều tác giả (Mangaka), trợ lý (Assistant), biên tập viên (Editor) và hội đồng (Board) cùng hoạt động, dữ liệu được phân luồng tự động thông qua các mối quan hệ khoá ngoại trong Cơ sở dữ liệu:

* **Tính riêng tư của Bộ truyện (Series):**
  * Mỗi Series thuộc về một Mangaka duy nhất thông qua cột `series.mangaka_id`. 
  * Mangaka A **chỉ nhìn thấy và có quyền chỉnh sửa** các Series của mình. Họ không thể xem bản thảo nháp hay giao việc trên các bộ truyện của Mangaka B (Được kiểm soát bởi hàm `checkOwnership` và `checkChapterOwnership` trong Controllers).
* **Định danh Giao việc (Task Assignment) & Cơ chế Bể trợ lý (Freelance Assistant Pool):**
  * Khi Mangaka A tạo một Task trên trang vẽ, họ sẽ chọn đích danh 1 Assistant (ví dụ: Assistant B) từ danh sách người dùng có vai trò `assistant` đang hoạt động (`active`) trong hệ thống.
  * **Cơ chế Bể trợ lý chung (Freelance Assistant Pool):** Tác giả được phép chọn bất kỳ trợ lý nào có trên hệ thống. Điều này mô phỏng mô hình vận hành linh hoạt của các tòa soạn truyện tranh lớn, nơi nhà xuất bản duy trì một danh sách trợ lý/cộng tác viên đã được kiểm định chất lượng đầu vào, cho phép tác giả tự do lựa chọn người phù hợp nhất cho từng phong cách vẽ của Task (ví dụ: trợ lý chuyên vẽ cảnh nền, chuyên đi nét, hoặc lên màu).
  * **Định hướng mở rộng tương lai (Scoping Team/Studio):** Khi quy mô dự án tăng lên và cần tính cách ly cao hơn (mỗi tác giả sở hữu một studio riêng với các trợ lý ruột), hệ thống có thể tích hợp thêm thực thể `teams` (Nhóm sáng tác) để liên kết cố định danh sách trợ lý khả dụng cho từng tác giả cụ thể.
  * Thông tin giao việc được lưu ở cột `tasks.assistant_id`. Lúc này, chỉ Assistant B mới nhìn thấy task này trong màn hình **"Công việc của tôi"** để nộp bài. Assistant C sẽ không hề thấy task này.
* **Biên tập viên (Tantou Editor):**
  * Editor C theo dõi tiến độ của các studio và viết đánh giá cho Chapter. Các đánh giá được lưu lại với thuộc tính `reviews.reviewer_id` để phân biệt Editor nào viết nhận xét nào.
* **Hội đồng biên tập (Editorial Board):**
  * Board D có quyền xem toàn bộ dự án để duyệt xuất bản và nhập dữ liệu bình chọn cho bảng xếp hạng chung toàn hệ thống.

---

## 2. Kịch Bản Minh Họa Quy Trình Nghiệp Vụ (Step-by-Step Scenario)

Để dễ hình dung và thuyết trình trước Hội đồng, đây là kịch bản chạy luồng thực tế giữa các tài khoản:

```
[Mangaka A]       ──(Giao Task cụ thể)──>   [Assistant B]
     │                                           │
(Nộp Chapter)                              (Nộp bản vẽ ZIP)
     ▼                                           ▼
[Editor C]        ──(Duyệt chất lượng)──>   [Mangaka A] (Duyệt)
     │
(Gửi báo cáo)
     ▼
[Board D]         ──(Duyệt xuất bản & Xếp hạng)
```

### Bước 1: Khởi động dự án mới
* **Mangaka A** đăng nhập hệ thống, tạo mới bộ truyện *"Đảo Hải Tặc"*. Trạng thái ban đầu của truyện là `planning` (Lên kế hoạch).
* **Editorial Board D** nhận được hồ sơ dự án của Mangaka A. Board D tiến hành duyệt xuất bản và cấu hình lịch phát hành là `weekly` (Hàng tuần). Trạng thái bộ truyện chuyển sang `ongoing` (Đang xuất bản).

### Bước 2: Sáng tác & Phân chia công việc cho Trợ lý
* **Mangaka A** tạo tiếp **Chapter 1** và upload **Trang vẽ số 1** (Page 1) lên hệ thống.
* Mangaka A sử dụng chuột để vẽ khoanh các phân vùng trực tiếp trên Trang vẽ số 1 (ví dụ: Vùng #1 là bong bóng thoại vẽ tay, Vùng #2 là khung nền nhân vật cần vẽ). Hệ thống lưu tọa độ các phân vùng thủ công này vào CSDL.
* Mangaka A chọn Vùng #2 (Khung nền), nhấn nút **"Giao việc"** để tạo một Task vẽ nền hậu cảnh. 
* Trong form tạo Task, Mangaka A chọn giao cho **Assistant B** và đặt hạn chót (Deadline).

### Bước 3: Trợ lý làm việc & Nộp bài
* **Assistant B** đăng nhập vào tài khoản trợ lý của mình. Hệ thống tự động nhận diện và gửi một Notification: *"Bạn có một Task mới được giao bởi Mangaka A"*.
* Assistant B vào mục **"Công việc của tôi"**, thấy Task vẽ nền đang ở trạng thái `pending` (Chờ xử lý). 
* Assistant B tải file ảnh nháp về, vẽ nền xong và đóng gói thành tệp ZIP.
* Assistant B bấm **"Nộp bài"**, tải file ZIP lên. Hệ thống tạo một record trong bảng `submissions` và tự động đổi trạng thái Task sang `in_progress`.

### Bước 4: Tác giả kiểm duyệt chất lượng vẽ của Trợ lý
* **Mangaka A** nhận được thông báo Assistant B đã nộp bài. 
* Mangaka A vào xem chi tiết Page 1, mở mục **"Quản lý công việc"** và nhấn xem bài nộp của Assistant B.
* Mangaka A viết nhận xét đánh giá:
  * Nếu bản vẽ đạt yêu cầu: Mangaka A bấm **Duyệt (Approve)**. Hệ thống tự động cập nhật trạng thái Task của Assistant B thành `completed` (Hoàn thành).
  * Nếu chưa đạt: Mangaka A bấm **Yêu cầu sửa đổi (Reject)** kèm mô tả lỗi để Assistant B vẽ lại.

### Bước 5: Nộp chương truyện cho Biên tập viên kiểm duyệt
* Sau khi toàn bộ các trang vẽ trong Chapter 1 được hoàn thiện, **Mangaka A** tiến hành nộp bản thảo nguyên chương (file PDF/ZIP chứa toàn bộ chapter) lên hệ thống. Trạng thái của Chapter 1 chuyển sang `reviewing` (Đang chờ duyệt).
* **Tantou Editor C** đăng nhập vào Dashboard tiến độ của biên tập viên, thấy Chapter 1 của Mangaka A đang đợi kiểm duyệt.
* Editor C xem bản thảo từng trang, sử dụng công cụ **vẽ khoanh vùng trực quan** để vẽ trực tiếp khung chữ nhật màu đỏ lên vùng bị lỗi trên ảnh trang truyện, viết ghi chú lỗi và lưu lại thông qua AJAX.
* **Mangaka A** vào xem chi tiết trang truyện của mình sẽ nhìn thấy các khung báo lỗi viền đứt nét màu đỏ bao quanh vị trí lỗi, di chuột qua để xem popover nội dung phản hồi từ Editor và sửa lỗi.
* Editor C đưa ra quyết định duyệt chuyên môn:
  * Nếu đạt tiêu chuẩn chất lượng: Editor C bấm **Duyệt (Approve)**. Trạng thái Chapter 1 chuyển sang `approved` (Đồng thời toàn bộ trang vẽ và ghi chú lỗi được khóa cứng, cấm chỉnh sửa).
  * Nếu chưa đạt: Editor C bấm **Từ chối (Reject)**. Trạng thái Chapter 1 tự động hoàn trả về `drawing` để tác giả và trợ lý tiếp tục vẽ lại.

### Bước 6: Đánh giá hiệu quả & Thù lao tháng
* Cuối tháng, **Editorial Board D** nhập dữ liệu bình chọn từ độc giả cho bộ truyện *"Đảo Hải Tặc"*.
* Hệ thống tự động cập nhật bảng xếp hạng. Nếu truyện tụt xuống vị trí thấp, hệ thống tự động gửi cảnh báo nguy cơ hủy truyện cho Mangaka A.
* **Assistant B** vào Dashboard cá nhân của mình để theo dõi thẻ thống kê: *"Thù lao & Số trang đã vẽ được duyệt trong tháng"*. Hệ thống tự động tính thù lao dựa trên số lượng Task đã được Mangaka A phê duyệt thành công trong tháng đó để làm cơ sở thanh toán thù lao khoán sản phẩm.

---

## 3. Bản chất thiết kế này giải quyết vấn đề gì?
1. **Tránh chồng chéo công việc:** Đảm bảo mỗi Task chỉ có 1 Assistant chịu trách nhiệm và chỉ 1 Mangaka sở hữu truyện được quyền kiểm duyệt.
2. **Quy trình đóng (Closed-loop workflow):** Dữ liệu di chuyển liên tục qua các trạng thái được ràng buộc chặt chẽ bằng nghiệp vụ, không ai có thể làm thay việc của ai.
3. **Minh bạch thông tin:** Tác giả nắm rõ tiến độ vẽ của trợ lý; Biên tập viên kiểm soát được thời gian nộp bài của tác giả; Ban giám đốc đo lường được hiệu quả của bộ truyện.
