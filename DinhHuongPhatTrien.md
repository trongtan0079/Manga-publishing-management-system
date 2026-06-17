# ĐỊNH HƯỚNG XÂY DỰNG HỆ THỐNG

## Manga Creation Workflow and Publishing Management System

---

# 1. Mục tiêu xây dựng hệ thống

Hệ thống được xây dựng nhằm hỗ trợ quản lý toàn bộ quy trình sáng tác, biên tập, kiểm duyệt và xuất bản Manga trên một nền tảng tập trung.

Hệ thống cho phép nhiều vai trò khác nhau phối hợp làm việc, theo dõi tiến độ thực hiện, quản lý nội dung và hỗ trợ ra quyết định xuất bản.

Các đối tượng sử dụng bao gồm:

* Admin
* Mangaka
* Assistant
* Tantou Editor
* Editorial Board

---

# 2. Định hướng kiến trúc hệ thống

Hệ thống được phát triển theo mô hình:

**Three-Tier Architecture kết hợp MVC**

## Presentation Layer

Lớp giao diện người dùng.

Chức năng:

* Hiển thị dữ liệu.
* Tiếp nhận thao tác từ người dùng.
* Điều hướng giữa các chức năng.

Thành phần:

```text
views/
assets/
```

---

## Business Logic Layer

Lớp xử lý nghiệp vụ.

Chức năng:

* Xử lý yêu cầu từ người dùng.
* Kiểm tra dữ liệu.
* Thực hiện các quy tắc nghiệp vụ.
* Điều phối luồng xử lý hệ thống.

Thành phần:

```text
controllers/
```

---

## Data Layer

Lớp quản lý dữ liệu.

Chức năng:

* Truy xuất dữ liệu.
* Thêm dữ liệu.
* Cập nhật dữ liệu.
* Xóa dữ liệu.

Thành phần:

```text
models/
database/
MySQL
```

---

# 3. Định hướng tổ chức mã nguồn

Cấu trúc dự án được tổ chức theo MVC nhằm đảm bảo khả năng bảo trì và mở rộng.

```text
MangaWorkflowSystem
│
├── controllers
├── models
├── views
├── config
├── assets
├── uploads
├── database
└── index.php
```

---

## Controllers

Mỗi Controller phụ trách một nhóm chức năng nghiệp vụ.

```text
AuthController
UserController
SeriesController
ChapterController
PageController
TaskController
SubmissionController
ReviewController
SeriesRankingController
NotificationController
```

Controller chịu trách nhiệm:

* Nhận request.
* Kiểm tra dữ liệu đầu vào.
* Gọi Model xử lý.
* Trả kết quả về View.

---

## Models

Mỗi Model đại diện cho một thực thể trong cơ sở dữ liệu.

```text
User
Role
Series
Chapter
Page
Task
Submission
Review
SeriesRanking
Notification
```

Model chịu trách nhiệm:

* Thao tác với cơ sở dữ liệu.
* Thực hiện CRUD.
* Truy vấn dữ liệu phục vụ nghiệp vụ.

---

## Views

Giao diện được tổ chức theo từng vai trò người dùng.

```text
views
│
├── admin
├── mangaka
├── assistant
├── editor
├── board
└── layouts
```

Cách tổ chức này giúp:

* Dễ phân quyền.
* Dễ quản lý giao diện.
* Dễ mở rộng chức năng theo từng vai trò.

---

# 4. Định hướng thiết kế cơ sở dữ liệu

Hệ thống sử dụng MySQL.

Các thực thể chính:

```text
Role
User
Series
Chapter
Page
Task
Submission
Review
SeriesRanking
Notification
```

Quan hệ chính:

```text
Role
 └── User

User
 ├── Series
 ├── Task
 ├── Submission
 ├── Review
 └── Notification

Series
 └── Chapter

Chapter
 └── Page

Task
 └── Submission

Submission
 └── Review

Series
 └── SeriesRanking
```

Thiết kế cơ sở dữ liệu được chuẩn hóa để giảm dư thừa dữ liệu và đảm bảo tính toàn vẹn.

---

# 5. Định hướng phát triển chức năng

Hệ thống được triển khai theo từng module độc lập.

## Giai đoạn 1

Authentication & Authorization

Chức năng:

* Login
* Logout
* Session
* Role Permission

---

## Giai đoạn 2

User Management

Chức năng:

* Quản lý tài khoản
* Quản lý phân quyền

---

## Giai đoạn 3

Series & Chapter Management

Chức năng:

* Tạo Series
* Quản lý Chapter
* Theo dõi tiến độ

---

## Giai đoạn 4

Page & Task Management

Chức năng:

* Upload Page
* Tạo Task
* Phân công công việc

---

## Giai đoạn 5

Submission & Review Management

Chức năng:

* Nộp Submission
* Kiểm duyệt Submission
* Gửi phản hồi chỉnh sửa

---

## Giai đoạn 6

Publishing & Ranking Management

Chức năng:

* Duyệt xuất bản
* Quản lý Ranking
* Thống kê hiệu suất

---

## Giai đoạn 7

Notification Management

Chức năng:

* Thông báo công việc
* Thông báo kiểm duyệt
* Thông báo xuất bản

---

# 6. Định hướng phân quyền người dùng

## Admin

* Quản lý User.
* Quản lý Role.
* Quản lý hệ thống.

## Mangaka

* Quản lý Series.
* Quản lý Chapter.
* Quản lý Page.
* Giao Task.

## Assistant

* Thực hiện Task.
* Nộp Submission.

## Tantou Editor

* Review Submission.
* Đánh giá Chapter.
* Yêu cầu chỉnh sửa.

## Editorial Board

* Duyệt xuất bản.
* Quản lý Ranking.
* Theo dõi hiệu suất Series.

---

# 7. Mục tiêu thiết kế

Hệ thống được xây dựng theo các nguyên tắc:

* Dễ sử dụng.
* Dễ bảo trì.
* Dễ mở rộng.
* Dễ kiểm thử.
* Dễ triển khai.
* Phù hợp với mô hình MVC.
* Phù hợp với kiến trúc Three-Tier Architecture.
* Hỗ trợ cộng tác giữa nhiều vai trò trong quy trình sản xuất Manga.

Đây nên được xem như **bản định hướng tổng thể trước khi code**, giúp toàn bộ nhóm thống nhất cách tổ chức hệ thống, cấu trúc thư mục, cơ sở dữ liệu và lộ trình triển khai.
