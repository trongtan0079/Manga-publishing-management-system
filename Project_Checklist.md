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

### 3. Models

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

---

### 4. Controllers

* [x] AuthController.php
* [x] UserController.php
* [x] SeriesController.php
* [x] ChapterController.php
* [x] PageController.php
* [ ] TaskController.php
* [ ] SubmissionController.php
* [ ] ReviewController.php
* [ ] SeriesRankingController.php
* [ ] NotificationController.php

---

### 5. Views

#### Layouts

* [ ] header.php
* [ ] footer.php
* [ ] navbar.php
* [ ] sidebar.php

#### Authentication

* [ ] login.php

#### Admin Module

* [ ] dashboard.php
* [ ] users.php
* [ ] roles.php

#### Mangaka Module

* [ ] dashboard.php
* [ ] series.php
* [ ] chapter.php
* [ ] pages.php
* [ ] assign_task.php

#### Assistant Module

* [ ] dashboard.php
* [ ] task_list.php
* [ ] upload_submission.php

#### Editor Module

* [ ] dashboard.php
* [ ] review_list.php
* [ ] review_detail.php

#### Editorial Board Module

* [ ] dashboard.php
* [ ] publish_series.php
* [ ] rankings.php

---

### 6. Core Features

#### Authentication & Authorization

* [ ] Login
* [ ] Logout
* [ ] Session Management
* [ ] Role-Based Access Control

#### Series Management

* [ ] Create Series
* [ ] Update Series
* [ ] Delete Series
* [ ] View Series

#### Chapter Management

* [ ] Create Chapter
* [ ] Update Chapter
* [ ] Publish Chapter

#### Page Management

* [ ] Upload Page
* [ ] Update Page
* [ ] Delete Page

#### Task Management

* [x] Assign Task
* [x] Update Task Status
* [x] View Task List

#### Submission Management

* [ ] Upload Submission
* [ ] View Submission
* [ ] Resubmit Submission

#### Review Management

* [ ] Approve Submission
* [ ] Reject Submission
* [ ] Add Review Comments

#### Ranking Management

* [ ] Evaluate Series
* [ ] Create Ranking
* [ ] View Ranking History

#### Notification Management

* [ ] Create Notification
* [ ] Mark as Read
* [ ] View Notifications

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
* [ ] User Manual

---

### 8. Testing

* [ ] Unit Testing
* [ ] Integration Testing
* [ ] User Acceptance Testing (UAT)
* [ ] Bug Fixing

---

### 9. Final Delivery

* [ ] Source Code Complete
* [ ] Database Complete
* [ ] Documentation Complete
* [ ] Slide Presentation Complete
* [ ] Demo Ready

---

## Current Progress

Completed:

* Database Design
* ERD
* Class Diagram
* Data Dictionary
* manga_workflow.sql

Current Task:

* Create Models

Next Task:

* Create Controllers
