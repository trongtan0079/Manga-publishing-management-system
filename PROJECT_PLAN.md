# PROJECT PLAN

## Manga Creation Workflow and Publishing Management System

### 1. Mục tiêu dự án

Xây dựng hệ thống hỗ trợ quản lý quy trình sáng tác, kiểm duyệt và xuất bản Manga, giúp tự động hóa việc phân công công việc, theo dõi tiến độ Chapter, quản lý Submission, Review, Ranking và Notification giữa các thành viên tham gia quy trình sản xuất Manga.

---

### 2. Công nghệ sử dụng

#### 2.1. Backend

* Ngôn ngữ: PHP 
* Kiến trúc: MVC (Model – View – Controller)
* Web Server: Apache

#### 2.2. Frontend

* HTML5
* CSS3
* JavaScript
* Bootstrap 5

#### 2.3. Database

* MySQL

#### 2.4. Công cụ phát triển

* Visual Studio Code
* XAMPP
* GitHub
* Jira
* Draw.io / PlantUML

---

### 3. Kiến trúc hệ thống

Hệ thống được xây dựng theo mô hình MVC bao gồm:

#### Model

Quản lý dữ liệu và thao tác với cơ sở dữ liệu.

#### View

Hiển thị giao diện người dùng.

#### Controller

Tiếp nhận yêu cầu từ người dùng và xử lý nghiệp vụ.

---

### 4. Phạm vi chức năng

#### Quản lý người dùng và phân quyền

* Đăng nhập
* Quản lý tài khoản
* Phân quyền hệ thống

#### Quản lý Series

* Tạo Series
* Cập nhật thông tin Series
* Theo dõi trạng thái Series

#### Quản lý Chapter

* Tạo Chapter
* Quản lý tiến độ Chapter
* Quản lý trạng thái Chapter

#### Quản lý Page

* Quản lý Manga Page
* Quản lý Artwork

#### Quản lý Task

* Tạo Task
* Giao Task
* Theo dõi tiến độ Task

#### Quản lý Submission

* Nộp Submission
* Kiểm duyệt Submission

#### Quản lý Review

* Review Chapter
* Ghi chú chỉnh sửa
* Theo dõi lịch sử Review

#### Quản lý xuất bản

* Phê duyệt xuất bản
* Lập lịch phát hành

#### Quản lý Ranking

* Cập nhật Ranking
* Theo dõi hiệu suất Series

#### Quản lý Notification

* Thông báo công việc
* Thông báo trạng thái Chapter
* Thông báo Review

---

### 5. Thiết kế cơ sở dữ liệu

Cơ sở dữ liệu bao gồm các thực thể chính:

* User
* Role
* Series
* Chapter
* Page
* Task
* Submission
* Review
* Ranking
* Notification

Thiết kế chi tiết được trình bày trong tài liệu ERD.

---

### 6. Kế hoạch triển khai

#### Giai đoạn 1: Phân tích yêu cầu

* Thu thập yêu cầu
* Xây dựng Use Case
* Xây dựng đặc tả yêu cầu

#### Giai đoạn 2: Thiết kế hệ thống

* Thiết kế kiến trúc hệ thống
* Thiết kế cơ sở dữ liệu
* Thiết kế UML Diagram

#### Giai đoạn 3: Phát triển hệ thống

* Xây dựng Database
* Xây dựng Backend
* Xây dựng Frontend

#### Giai đoạn 4: Kiểm thử

* Unit Testing
* Integration Testing
* System Testing

#### Giai đoạn 5: Triển khai và nghiệm thu

* Hoàn thiện hệ thống
* Chuẩn bị tài liệu
* Báo cáo và bảo vệ dự án

---

### 7. Quản lý mã nguồn

* Sử dụng GitHub để quản lý source code.
* Không commit trực tiếp lên nhánh main.
* Mỗi chức năng được phát triển trên một nhánh riêng.
* Tạo Pull Request trước khi merge.
* Commit message phải rõ ràng và mô tả đúng chức năng được thực hiện.

---

### 8. Quản lý công việc

Sử dụng Jira để:

* Quản lý Sprint
* Quản lý Epic
* Quản lý Task
* Theo dõi tiến độ thực hiện
* Quản lý Bug và Issue

Các thành viên phải cập nhật trạng thái công việc thường xuyên để đảm bảo tiến độ dự án.

---

### 9. Kết quả mong đợi

* Hệ thống hoạt động ổn định.
* Quản lý đầy đủ quy trình sáng tác và xuất bản Manga.
* Hỗ trợ nhiều vai trò người dùng.
* Đảm bảo tính bảo mật và khả năng mở rộng.
* Đáp ứng các yêu cầu chức năng và phi chức năng đã đề ra.
