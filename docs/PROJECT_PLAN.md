# PROJECT PLAN BONUS

# Manga Creation Workflow and Publishing Management System

---

# 1. Project Overview

## 1.1 Project Objective

Manga Creation Workflow and Publishing Management System là hệ thống hỗ trợ quản lý toàn bộ quy trình sáng tác, biên tập, kiểm duyệt và xuất bản Manga.

Hệ thống giúp tự động hóa việc quản lý công việc, theo dõi tiến độ thực hiện Chapter, kiểm duyệt Submission, quản lý Ranking và Notification giữa các thành viên tham gia quy trình sản xuất Manga.

Mục tiêu của dự án là:

* Nâng cao hiệu quả quản lý quy trình sản xuất Manga.
* Hỗ trợ cộng tác giữa nhiều vai trò trong cùng một dự án Manga.
* Giảm thời gian theo dõi tiến độ và xử lý công việc thủ công.
* Tăng khả năng kiểm soát chất lượng nội dung trước khi xuất bản.
* Cung cấp dữ liệu thống kê và xếp hạng hỗ trợ đánh giá hiệu suất Series.

---

# 2. Technology Stack

## 2.1 Backend

* PHP 8.x
* Apache Web Server
* MVC Pattern

## 2.2 Frontend

* HTML5
* CSS3
* JavaScript
* Bootstrap 5

## 2.3 Database

* MySQL

## 2.4 Development Tools

* Visual Studio Code
* XAMPP
* GitHub
* Jira
* Antigravity
* Draw.io
* PlantUML

---

# 3. System Architecture

## 3.1 Software Architecture

Hệ thống được xây dựng theo mô hình kiến trúc ba lớp (Three-Tier Architecture).

### Presentation Layer

Cung cấp giao diện tương tác cho các đối tượng sử dụng:

* Admin
* Mangaka
* Assistant
* Tantou Editor
* Editorial Board

### Business Logic Layer

Thực hiện xử lý nghiệp vụ:

* User Management
* Series Management
* Chapter Management
* Page Management
* Task Management
* Submission Management
* Review Management
* Ranking Management
* Notification Management

Tầng xử lý nghiệp vụ được tổ chức theo mô hình MVC (Model – View – Controller).

### Data Layer

Quản lý việc lưu trữ, truy xuất và cập nhật dữ liệu trong hệ quản trị cơ sở dữ liệu MySQL.

---

## 3.2 Deployment Architecture

Hệ thống được triển khai theo mô hình Client – Server.

### Client

Người dùng truy cập hệ thống thông qua trình duyệt Web.

### Application Server

Tiếp nhận yêu cầu từ người dùng, xử lý nghiệp vụ và tương tác với cơ sở dữ liệu.

### Database Server

Lưu trữ toàn bộ dữ liệu của hệ thống.

---

# 4. User Roles

## 4.1 Admin

* Quản lý tài khoản người dùng.
* Quản lý vai trò và phân quyền.
* Giám sát hoạt động hệ thống.

## 4.2 Mangaka

* Tạo và quản lý Series.
* Tạo Chapter.
* Quản lý Page.
* Giao Task cho Assistant.
* Theo dõi tiến độ thực hiện.

## 4.3 Assistant

* Nhận Task được phân công.
* Thực hiện công việc.
* Nộp Submission.

## 4.4 Tantou Editor

* Kiểm tra Submission.
* Thực hiện Review.
* Gửi yêu cầu chỉnh sửa.
* Đánh giá chất lượng nội dung.

## 4.5 Editorial Board

* Phê duyệt xuất bản.
* Lập lịch phát hành.
* Quản lý Ranking.
* Theo dõi hiệu suất Series.

---

# 5. Functional Scope

## 5.1 Authentication & Authorization

* Login
* Logout
* Role-based Access Control

## 5.2 User Management

* Create User
* Update User
* Manage User Status
* Manage Roles

## 5.3 Series Management

* Create Series
* Update Series
* View Series Details
* Track Series Status

## 5.4 Chapter Management

* Create Chapter
* Update Chapter
* Track Chapter Progress
* Manage Chapter Status

## 5.5 Page Management

* Upload Manga Pages
* Manage Artwork Files
* Organize Page Sequence

## 5.6 Task Management

* Create Task
* Assign Task
* Update Task Status
* Track Task Progress

## 5.7 Submission Management

* Submit Work
* View Submission History
* Track Submission Status

## 5.8 Review Management

* Review Submission
* Add Feedback
* Request Revisions
* Track Review History

## 5.9 Publishing Management

* Approve Publication
* Schedule Release
* Publish Series

## 5.10 Ranking Management

* Manage Ranking Data
* View Ranking Reports
* Analyze Series Performance

## 5.11 Notification Management

* Task Notifications
* Review Notifications
* Publishing Notifications
* System Notifications

---

# 6. Database Design

Các thực thể chính của hệ thống:

* User
* Role
* Series
* Chapter
* Page
* Task
* Submission
* Review
* SeriesRanking
* Notification

Thiết kế chi tiết được mô tả trong tài liệu ERD và Database Design Specification.

---

# 7. Project Implementation Plan

## Phase 1 – Requirements Analysis

Activities:

* Requirement Elicitation
* Requirement Analysis
* Use Case Modeling
* SRS Documentation

Deliverables:

* Software Requirement Specification (SRS)
* Use Case Diagram

---

## Phase 2 – System Design

Activities:

* Architecture Design
* Database Design
* UML Modeling

Deliverables:

* ERD
* Class Diagram
* Activity Diagram
* Sequence Diagram
* Architecture Design Document

---

## Phase 3 – Development

Activities:

* Database Implementation
* Backend Development
* Frontend Development
* Integration

Deliverables:

* Source Code
* Database Script

---

## Phase 4 – Testing

Activities:

* Unit Testing
* Integration Testing
* System Testing
* Bug Fixing

Deliverables:

* Test Cases
* Test Report

---

## Phase 5 – Deployment & Project Closure

Activities:

* System Deployment
* Documentation Finalization
* Project Presentation

Deliverables:

* Final System
* User Manual
* Final Report
* Presentation Slides

---

# 8. Source Code Management

* GitHub được sử dụng để quản lý mã nguồn.
* Không commit trực tiếp lên nhánh main.
* Mỗi chức năng được phát triển trên một feature branch riêng.
* Thực hiện Pull Request trước khi merge.
* Commit message phải mô tả rõ nội dung thay đổi.

Ví dụ:

Branch:
feature/MGS-01-login

Commit:
MGS-01 Complete Login Module

GitHub được tích hợp với Jira và Antigravity để hỗ trợ quản lý quy trình phát triển.

---

# 9. Project Management

Công cụ quản lý công việc:

* Jira
* GitHub
* Antigravity

Workflow:

To Do → In Progress → Review → Testing → Done

Jira được sử dụng để:

* Quản lý Sprint
* Quản lý Epic
* Quản lý Task
* Theo dõi tiến độ
* Quản lý Bug và Issue

---

# 10. Expected Outcomes

* Hệ thống hoạt động ổn định.
* Hỗ trợ đầy đủ quy trình sáng tác và xuất bản Manga.
* Hỗ trợ nhiều vai trò người dùng.
* Đảm bảo tính bảo mật và khả năng mở rộng.
* Đáp ứng đầy đủ các yêu cầu chức năng và phi chức năng.
* Cung cấp môi trường cộng tác hiệu quả cho nhóm sản xuất Manga.
