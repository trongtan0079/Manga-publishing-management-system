# PROJECT PLAN BONUS

# Manga Creation Workflow and Publishing Management System

---

## 1. Mục tiêu dự án

Dự án nhằm xây dựng hệ thống hỗ trợ quản lý quy trình sáng tác, kiểm duyệt và xuất bản Manga.

Hệ thống giúp tự động hóa việc phân công công việc, theo dõi tiến độ Chapter, quản lý Submission, Review, Ranking và Notification giữa các thành viên tham gia quy trình sản xuất Manga.

---

## 2. Công nghệ sử dụng

### 2.1 Backend

* Ngôn ngữ: PHP 8.x
* Kiến trúc: MVC (Model – View – Controller)
* Web Server: Apache

### 2.2 Frontend

* HTML5
* CSS3
* JavaScript
* Bootstrap 5

### 2.3 Database

* MySQL

### 2.4 Công cụ phát triển

* Visual Studio Code
* XAMPP
* GitHub
* Jira
* Antigravity
* Draw.io / PlantUML

---

## 3. Kiến trúc hệ thống

Hệ thống được xây dựng theo mô hình MVC.

### Model

Quản lý dữ liệu và thao tác với cơ sở dữ liệu.

### View

Hiển thị giao diện người dùng.

### Controller

Tiếp nhận yêu cầu từ người dùng và xử lý business logic.

---

## 4. Phạm vi chức năng

### 4.1 Quản lý người dùng và phân quyền

* Đăng nhập
* Quản lý tài khoản
* Phân quyền hệ thống

### 4.2 Quản lý Series

* Tạo Series
* Cập nhật thông tin Series
* Theo dõi trạng thái Series

### 4.3 Quản lý Chapter

* Tạo Chapter
* Theo dõi tiến độ Chapter
* Quản lý trạng thái Chapter

### 4.4 Quản lý Page

* Quản lý Manga Page
* Quản lý Artwork

### 4.5 Quản lý Task

* Tạo Task
* Giao Task
* Theo dõi tiến độ Task

### 4.6 Quản lý Submission

* Nộp Submission
* Kiểm duyệt Submission

### 4.7 Quản lý Review

* Review Chapter
* Ghi chú chỉnh sửa
* Theo dõi lịch sử Review

### 4.8 Quản lý xuất bản

* Phê duyệt xuất bản
* Lập lịch phát hành

### 4.9 Quản lý Ranking

* Cập nhật Ranking
* Theo dõi hiệu suất Series

### 4.10 Quản lý Notification

* Thông báo công việc
* Thông báo trạng thái Chapter
* Thông báo Review

---

## 5. Thiết kế cơ sở dữ liệu

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

## 6. Kế hoạch triển khai

### Giai đoạn 1: Phân tích yêu cầu

* Thu thập yêu cầu
* Xây dựng Use Case Diagram
* Xây dựng tài liệu đặc tả yêu cầu

### Giai đoạn 2: Thiết kế hệ thống

* Thiết kế kiến trúc hệ thống
* Thiết kế cơ sở dữ liệu
* Thiết kế UML Diagram

### Giai đoạn 3: Phát triển hệ thống

* Xây dựng Database
* Xây dựng Backend
* Xây dựng Frontend

### Giai đoạn 4: Kiểm thử hệ thống

* Unit Testing
* Integration Testing
* System Testing

### Giai đoạn 5: Triển khai và nghiệm thu

* Hoàn thiện hệ thống
* Chuẩn bị tài liệu
* Báo cáo và bảo vệ dự án

---

## 7. Checklist triển khai chức năng

### Authentication

* [ ] Login
* [ ] Logout
* [ ] Role Permission

### Series Management

* [ ] Create Series
* [ ] Update Series
* [ ] Track Series Status

### Chapter Management

* [ ] Create Chapter
* [ ] Track Chapter Progress
* [ ] Manage Chapter Status

### Task Management

* [ ] Create Task
* [ ] Assign Task
* [ ] Update Task Progress

### Submission and Review

* [ ] Submit Artwork
* [ ] Review Submission
* [ ] Revision Tracking

### Notification System

* [ ] Task Notification
* [ ] Review Notification
* [ ] System Notification

---

## 8. Quản lý mã nguồn

* Sử dụng GitHub để quản lý source code.
* Không commit trực tiếp lên nhánh main.
* Mỗi chức năng được phát triển trên một branch riêng.
* Tạo Pull Request trước khi merge vào develop/main.
* Commit message phải mô tả rõ chức năng được thực hiện.

Ví dụ:

* Branch: feature/MGS-01-login
* Commit: MGS-01 complete login module

GitHub được liên kết với Jira và Antigravity nhằm hỗ trợ quản lý workflow, theo dõi tiến độ và kiểm soát quá trình phát triển hệ thống.

---

## 9. Quản lý công việc

Sử dụng Jira để:

* Quản lý Sprint
* Quản lý Epic
* Quản lý Task
* Theo dõi tiến độ thực hiện
* Quản lý Bug và Issue

Workflow quản lý công việc:

To Do → In Progress → Review → Testing → Done

Mỗi Jira Task sẽ được liên kết với branch, commit và Pull Request trên GitHub nhằm đồng bộ tiến độ phát triển với Antigravity Workflow.

Các thành viên phải cập nhật trạng thái công việc thường xuyên để đảm bảo tiến độ dự án.

---

## 10. Kết quả mong đợi

* Hệ thống hoạt động ổn định.
* Quản lý đầy đủ quy trình sáng tác và xuất bản Manga.
* Hỗ trợ nhiều vai trò người dùng.
* Đảm bảo tính bảo mật và khả năng mở rộng.
* Đáp ứng đầy đủ các yêu cầu chức năng và phi chức năng.
