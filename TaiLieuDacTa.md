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

*Tùy chọn: Tích hợp AI
- AI tự động tô màu trang truyện
- AI hỗ trợ phân đoạn vùng trên trang truyện"	

"Series
Chapter
Page
Manuscript
Task
Submission
..."	
Yes	"RQ: Which deep learning architecture achieves the highest accuracy in detecting and segmenting panels, speech bubbles, and character regions in manga pages?

Sub-RQs:
- RQ1: How do U-Net, YOLOv8, SAM, etc. differ in segmentation accuracy (IoU, F1-score) across manga region types (panels, speech bubbles, characters)?
- RQ2: How does variation in manga art styles affect the segmentation performance of each architecture?"



1.4 Đối tượng sử dụng
1.4.1 Admin  
Admin là người quản trị hệ thống, chịu trách nhiệm quản lý người dùng, phân quyền truy cập và đảm bảo hệ thống hoạt động ổn định. Admin có quyền kiểm soát các chức năng quản trị và theo dõi hoạt động chung của hệ thống.

- Quản lý tài khoản người dùng
- Quản lý vai trò và phân quyền
- Theo dõi hoạt động hệ thống
- Xem thông báo hệ thống
- Xem báo cáo và thống kê tổng hợp

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
- Bỏ phiếu xét duyệt các Series trước khi phát hành
- Nhập dữ liệu bình chọn của độc giả
- Theo dõi bảng xếp hạng các Series
- Xem báo cáo thống kê kết quả phát hành
- Đưa ra quyết định tiếp tục xuất bản hoặc ngừng phát hành Series
- Theo dõi các thông báo liên quan đến hoạt động xuất bản