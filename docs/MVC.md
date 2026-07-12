Manga-publishing-management-system/
│
├── controllers/
│   ├── AuthController.php
│   ├── UserController.php
│   ├── SeriesController.php
│   ├── ChapterController.php
│   ├── PageController.php
│   ├── TaskController.php
│   ├── SubmissionController.php
│   ├── ReviewController.php
│   ├── SeriesRankingController.php
│   └── NotificationController.php
│
├── models/
│   ├── User.php
│   ├── Role.php
│   ├── Series.php
│   ├── Chapter.php
│   ├── Page.php
│   ├── Task.php
│   ├── Submission.php
│   ├── Review.php
│   ├── SeriesRanking.php
│   └── Notification.php
│
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── navbar.php
│   │   └── sidebar.php
│   │
│   ├── auth/
│   │   └── login.php
│   │
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── users.php
│   │   └── roles.php
│   │
│   ├── mangaka/
│   │   ├── dashboard.php
│   │   ├── series.php
│   │   ├── chapter.php
│   │   ├── pages.php
│   │   ├── task_create.php
│   │   └── task_edit.php
│   │
│   ├── assistant/
│   │   ├── dashboard.php
│   │   ├── task_list.php
│   │   └── upload_submission.php
│   │
│   ├── editor/
│   │   ├── dashboard.php
│   │   ├── review_list.php
│   │   └── review_detail.php
│   │
│   └── board/
│       ├── dashboard.php
│       ├── publish_series.php
│       └── rankings.php
│
├── config/
│   └── database.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── uploads/
│   ├── pages/
│   └── submissions/
│
├── database/
│   └── manga_workflow.sql
│
├── index.php
└── README.md
