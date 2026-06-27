# Manga Creation Workflow and Publishing Management System

## Project Progress Checklist

### 1. Database Design

* [x] Phân tích nghiệp vụ hệ thống
* [x] Xác định các thực thể (Entities)
* [x] Xây dựng ERD (Entity Relationship Diagram)
* [x] Xây dựng Class Diagram
* [x] Chuẩn hóa cơ sở dữ liệu (3NF)
* [x] Thiết kế Data Dictionary
* [x] Thiết kế khóa chính (PK)
* [x] Thiết kế khóa ngoại (FK)
* [x] Xây dựng file database/manga_workflow.sql
* [x] Đồng bộ ERD - Class Diagram - SQL

---

### 2. Project Structure

* [x] Thiết kế cấu trúc thư mục MVC
* [x] Tạo project framework
* [x] Cấu hình config/database.php
* [x] Tạo file index.php
* [x] Thiết lập routing cơ bản

---

### 3. Models (Data Access)

* [x] Role.php
* [x] User.php
* [x] Series.php
* [x] Chapter.php
* [x] Page.php
* [x] Task.php
* [x] Submission.php
* [x] Review.php
* [x] SeriesRanking.php
* [x] Notification.php
* *Lưu ý: Đang sử dụng cơ chế PDO::FETCH_ASSOC để đảm bảo tiến độ. Sẽ có nhánh riêng (feature/refactor-oop) sau khi bảo vệ dự án.*

---

### 4. Controllers

* [x] AuthController.php
* [x] UserController.php
* [x] SeriesController.php
* [x] ChapterController.php
* [x] PageController.php
* [x] TaskController.php
* [x] SubmissionController.php
* [x] ReviewController.php
* [x] SeriesRankingController.php
* [x] NotificationController.php
* [x] DashboardController.php

---

### 5. Views

#### Layouts
* [x] header.php
* [x] footer.php
* [x] navbar.php
* [x] sidebar.php

#### Authentication
* [x] login.php

#### Admin Module
* [x] dashboard.php
* [x] users.php (Create, Edit, Detail)
* [x] roles.php

#### Mangaka Module
* [x] dashboard.php
* [x] series.php & series_detail.php
* [x] chapter_create.php & chapter_detail.php & chapter_edit.php
* [x] page_create.php & page_detail.php & page_edit.php
* [x] task_create.php & task_edit.php
* [x] rankings.php

#### Assistant Module
* [x] dashboard.php
* [x] task_list.php
* [x] upload_submission.php

#### Editor Module
* [x] dashboard.php
* [x] review_list.php & review_create.php
* [x] submission_list.php & submission_detail.php
* [x] review_detail.php

#### Editorial Board Module
* [x] dashboard.php
* [x] rankings.php & ranking_detail.php
* [x] ranking_create.php & ranking_edit.php

---

### 6. Core Features

#### Authentication & Authorization
* [x] Login & Logout
* [x] Session Management
* [/] Role-Based Access Control (Đang hoàn thiện - Priority)

#### Series Management
* [x] Create, Update, Delete, View Series

#### Chapter Management
* [x] Create, Update, Publish Chapter

#### Page Management
* [x] Upload, Update, Delete Page (Đã fix lỗi trạng thái)

#### Task Management
* [x] Assign Task
* [x] Update Task Status
* [x] View Task List

#### Submission Management
* [x] Upload Submission
* [x] View Submission
* [x] Resubmit Submission

#### Review Management
* [x] Approve/Reject Submission
* [x] Add Review Comments

#### Ranking Management
* [x] Evaluate Series
* [x] Create Ranking
* [x] View Ranking History

#### Notification Management
* [x] Create Notification
* [x] Mark as Read
* [x] View Notifications

---

### 7. UML & Documentation

* [x] ERD
* [x] Class Diagram
* [ ] Use Case Diagram
* [ ] Use Case Description
* [ ] Activity Diagram
* [ ] Sequence Diagram
* [ ] System Architecture Diagram
* [ ] Database Documentation
* [ ] User Manual (Tài liệu hướng dẫn)

---

### 8. Testing & QA (Giai Đoạn Nước Rút)

* [ ] Kiểm thử End-to-End (E2E) toàn bộ luồng nghiệp vụ
* [ ] Fix bugs (Giao diện & Logic)
* [ ] Kiểm tra phân quyền (Role Permission) trên toàn hệ thống
* [ ] Hoàn thiện hiển thị Dashboard cho 5 role

---

### 9. Final Delivery

* [x] Hoàn thiện Source Code (Mức độ tính năng cơ bản)
* [x] Hoàn thiện CSDL
* [ ] Chuẩn bị dữ liệu Demo
* [ ] Hoàn thiện Documentation
* [ ] Sẵn sàng Demo (Demo Ready)
* [ ] Báo cáo / Slide Presentation

---

## Lộ Trình Giai Đoạn Hiện Tại (Nước Rút)

**Trạng thái hệ thống:** Đã hoàn thiện gần như toàn bộ các module nghiệp vụ, Models, Controllers và Views hoạt động ổn định. Giao diện không còn báo lỗi IDE.

**6 Ưu Tiên Hàng Đầu (Hiện tại):**
1. [ ] Hoàn thiện toàn bộ workflow nghiệp vụ (Review & Cross-check).
2. [ ] Kiểm thử End-to-End các quy trình (Mangaka -> Assistant -> Mangaka -> Editor -> Board).
3. [ ] Sửa các bug phát sinh trong quá trình kiểm thử.
4. [ ] Rà soát và hoàn thiện giao diện Dashboard của tất cả các Role.
5. [ ] Hoàn thiện Role Permission (Đảm bảo an toàn phân quyền ở mọi endpoint).
6. [ ] Chuẩn bị dữ liệu demo (Sample data) và tài liệu hệ thống.

**Kế Hoạch Tương Lai (Post-Demo):**
- Checkout nhánh `feature/refactor-oop`.
- Chuyển đổi toàn bộ kiến trúc Data Access từ `PDO::FETCH_ASSOC` (Mảng) sang Object Mapping (OOP) để tuân thủ 100% `Database_design.md`.
