# TÀI LIỆU ĐẶC TẢ CHI TIẾT CA SỬ DỤNG (USE CASE DESCRIPTION)

Tài liệu này đặc tả chi tiết các Ca sử dụng (Use Cases) cốt lõi của hệ thống MangaPMS để phục vụ thuyết trình và làm rõ luồng hoạt động hệ thống.

---

## 1. UC_Editor_Annotate: Đánh dấu lỗi trực quan trên trang truyện

*   **Tác nhân (Actor):** Tantou Editor (Biên tập viên chuyên trách)
*   **Mô tả ngắn:** Cho phép Biên tập viên vẽ khoanh vùng hình chữ nhật và ghi chú các lỗi cụ thể về nét vẽ, thoại, hoặc kịch bản trực tiếp trên từng trang truyện hoàn chỉnh của Chapter.
*   **Tiền điều kiện (Preconditions):**
    1.  Biên tập viên đã đăng nhập hệ thống và được gán phụ trách Series tương ứng.
    2.  Chapter đang ở trạng thái **Đang chờ duyệt (`reviewing`)**.
    3.  Chapter đã có ít nhất một trang truyện hoàn thiện được upload bởi Mangaka.
*   **Kịch bản chính (Basic Flow):**
    1.  Editor truy cập vào trang chi tiết bản thảo (`submission_detail`).
    2.  Hệ thống hiển thị danh sách các trang truyện hoàn thiện thuộc Chapter.
    3.  Editor nhấn nút **"Đánh dấu lỗi"** bên dưới trang truyện muốn nhận xét. Hệ thống hiển thị Modal Canvas chứa ảnh trang truyện đó.
    4.  Editor click giữ và kéo chuột vẽ một khung hình chữ nhật bao quanh vùng bị lỗi trên ảnh.
    5.  Hệ thống hiển thị ô nhập ghi chú bên cạnh khung vẽ. Editor gõ nội dung mô tả lỗi (ví dụ: "Sai chính tả ô thoại này", "Nét vẽ chưa sạch nền").
    6.  Editor nhấn nút **"Lưu ghi chú"**.
    7.  Hệ thống quy đổi tọa độ về kích thước chuẩn (`800 x 1000 pixels`), gửi request AJAX lên server.
    8.  Hệ thống lưu trữ thành công vào bảng `editor_annotations`, phản hồi thành công và vẽ khung đỏ cố định lên Modal để xác nhận.
*   **Kịch bản ngoại lệ (Exception Flows):**
    *   *Trường hợp Chapter đã được phê duyệt (`approved`/`published`):* API từ chối xử lý, trả về mã lỗi `"Chương truyện đã được phê duyệt hoặc xuất bản, không thể chỉnh sửa ghi chú lỗi"`.
*   **Hậu điều kiện (Postconditions):** Ghi chú báo lỗi được lưu trữ cố định và hiển thị trực quan dưới dạng khung đứt nét đỏ kèm popover trên màn hình của Mangaka.

---

## 2. UC_Mangaka_Region: Vẽ phân vùng bản vẽ thủ công

*   **Tác nhân (Actor):** Mangaka (Họa sĩ chính)
*   **Mô tả ngắn:** Mangaka tự tay phân chia các khu vực trên trang truyện thô (khung thoại, nhân vật, bối cảnh) để giao Task vẽ chi tiết cho Assistant.
*   **Tiền điều kiện (Preconditions):**
    1.  Mangaka đã đăng nhập và là chủ sở hữu của Series.
    2.  Chapter chứa trang vẽ đang ở trạng thái **Đang vẽ (`drawing`)** hoặc **Bản nháp (`drafting`)**.
*   **Kịch bản chính (Basic Flow):**
    1.  Mangaka mở chi tiết một Trang truyện (`page_detail`).
    2.  Tại khu vực ảnh chính, Mangaka nhấn và kéo chuột để khoanh vùng khu vực cần phân công (ví dụ: bối cảnh tòa nhà phía sau).
    3.  Hệ thống hiển thị bảng pop-up nhỏ khi thả chuột.
    4.  Mangaka chọn loại phân vùng (ví dụ: `background` - Vẽ nền) và nhấn **"Lưu phân vùng"**.
    5.  Hệ thống lưu tọa độ tương đối của phân vùng vào bảng `page_regions` và hiển thị khung nét đứt màu xanh dương để đánh dấu.
*   **Kịch bản ngoại lệ (Exception Flows):**
    *   *Trường hợp Chapter đang bị khóa duyệt (`reviewing` / `approved` / `published`):* Hệ thống ẩn công cụ vẽ và từ chối lưu phân vùng mới.
*   **Hậu điều kiện (Postconditions):** Phân vùng mới được tạo thành công ở trạng thái `pending` (Chờ giao việc), sẵn sàng để Mangaka bấm nút liên kết giao Task cho Assistant.

---

## 3. UC_Assistant_Submit: Nộp sản phẩm hoàn thiện (Submission)

*   **Tác nhân (Actor):** Assistant (Trợ lý vẽ)
*   **Mô tả ngắn:** Assistant tải tệp vẽ đã hoàn thành (file ảnh hoặc file nén `.zip` / `.pdf`) lên hệ thống để Mangaka kiểm duyệt.
*   **Tiền điều kiện (Preconditions):**
    1.  Assistant đã đăng nhập hệ thống.
    2.  Có Task được giao đích danh đang ở trạng thái `pending` hoặc `in_progress`.
    3.  Chapter chứa trang vẽ đó đang ở trạng thái **Đang vẽ (`drawing`)**.
*   **Kịch bản chính (Basic Flow):**
    1.  Assistant truy cập mục **"Công việc của tôi"** (`task_list`).
    2.  Chọn Task cần nộp và nhấn **"Nộp bài"**.
    3.  Assistant chọn file từ máy tính và viết ghi chú đính kèm (nếu có).
    4.  Nhấn nút **"Tải lên bản vẽ"**.
    5.  Hệ thống kiểm tra kích thước file (tối đa 20MB) và phần mở rộng file (jpg, png, pdf, zip).
    6.  Hệ thống lưu file vật lý vào thư mục `/uploads/submissions/`, lưu thông tin bản nộp vào bảng `submissions`.
    7.  Hệ thống tự động chuyển trạng thái của Task liên kết sang **Đang xử lý/Kiểm tra (`in_progress`)**.
    8.  Hệ thống gửi thông báo tự động cho Mangaka để thông báo có bài nộp mới.
*   **Kịch bản ngoại lệ (Exception Flows):**
    *   *Trường hợp file vượt quá kích thước hoặc sai định dạng:* Hệ thống hủy bỏ request, thông báo lỗi cụ thể cho Assistant vẽ lại.
    *   *Trường hợp Chapter đã khóa duyệt:* Hệ thống chặn đứng form upload.
*   **Hậu điều kiện (Postconditions):** Bản nộp ở trạng thái `pending` chờ Mangaka vào chấm điểm, phê duyệt hoặc từ chối yêu cầu sửa đổi.
