# Cấu trúc thư mục Manga Publishing Management System (Manga PMS)

```text
Manga-publishing-management-system/
├── UML
│   ├── Activity_Diagram_Quy_Trình_Giao_Và_Thực_Hiện_Task.puml
│   ├── Activity_Diagram_quy trình_Review_Chapter.puml
│   ├── Activity_Diagram_quy_trình_xuất_bản_Manga.puml
│   ├── Class_Diagram.puml
│   ├── Deployment_Diagram.puml
│   ├── ERD.puml
│   ├── State_Machine_Chapter.puml
│   ├── State_Machine_Page.puml
│   ├── State_Machine_Page_Region.puml
│   ├── State_Machine_Series.puml
│   ├── State_Machine_Task.puml
│   ├── Swimlane_tong_quat_quy_trinh_sang_tac_maga.puml
│   ├── System_Architecture_Diagram.puml
│   ├── Use_Case_Diagram.puml
│   ├── sequence_diagram_dang_nhap_he_thong.puml
│   ├── sequence_diagram_manga_series_publishing.puml
│   ├── sequence_diagram_manga_task_assignment.puml
│   ├── sequence_diagram_quy_trinh_nop_submission.puml
│   └── sequence_diagram_review_chapter.puml
├── assets
│   ├── css
│   │   └── style.css
│   ├── images
│   │   ├── .gitkeep
│   │   ├── Manga_dashboard_0.png
│   │   ├── Manga_dashboard_0_1.jpg
│   │   ├── Manga_dashboard_0_2.png
│   │   ├── Manga_dashboard_0_3.jpg
│   │   ├── Manga_dashboard_1.png
│   │   ├── Manga_dashboard_10.png
│   │   ├── Manga_dashboard_11.png
│   │   ├── Manga_dashboard_12.png
│   │   ├── Manga_dashboard_2.png
│   │   ├── Manga_dashboard_3.png
│   │   ├── Manga_dashboard_4.png
│   │   ├── Manga_dashboard_5.png
│   │   ├── Manga_dashboard_6.png
│   │   ├── Manga_dashboard_7.png
│   │   ├── Manga_dashboard_8.png
│   │   ├── Manga_dashboard_9.png
│   │   ├── anhnen_login.webp
│   │   └── manga_workflow_swimlane.png
│   └── js
│       └── main.js
├── config
│   └── database.php
├── controllers
│   ├── AuthController.php
│   ├── BaseController.php
│   ├── ChapterController.php
│   ├── DashboardController.php
│   ├── NotificationController.php
│   ├── PageController.php
│   ├── PageRegionController.php
│   ├── ReviewController.php
│   ├── SeriesController.php
│   ├── SeriesRankingController.php
│   ├── SubmissionController.php
│   ├── TaskController.php
│   └── UserController.php
├── core
│   ├── Auth.php
│   └── Model.php
├── database
│   ├── add_editor_annotations_table.sql
│   ├── add_old_image_url_to_pages.sql
│   ├── alter_chapter_enums.php
│   ├── alter_enums.php
│   ├── create_board_votes_table.sql
│   ├── create_submission_annotations_table.sql
│   ├── manga_workflow.sql
│   ├── run_migration.php
│   └── seed_users.sql
├── docker
│   ├── db
│   │   └── init
│   │       ├── 02_seed_users.sql
│   │       ├── 03_editor_annotations.sql
│   │       ├── 04_submission_annotations.sql
│   │       └── 05_board_votes.sql
│   └── php
│       └── Dockerfile
├── docs
│   ├── admin
│   │   ├── admin_bug_list.md
│   │   ├── admin_dashboard_report.md
│   │   ├── admin_test_result.md
│   │   ├── admin_verification.md
│   │   ├── module_summary.md
│   │   ├── roles_report.md
│   │   └── walkthrough.md
│   ├── authentication
│   │   ├── authentication_bug_list.md
│   │   ├── authentication_report.md
│   │   ├── authentication_test_result.md
│   │   ├── authentication_verification.md
│   │   ├── module_summary.md
│   │   ├── permission_report.md
│   │   ├── user_management_report.md
│   │   └── walkthrough.md
│   ├── mangaka
│   │   ├── mangaka_bug_list.md
│   │   ├── mangaka_report.md
│   │   ├── mangaka_test_result.md
│   │   ├── mangaka_verification.md
│   │   ├── module_summary.md
│   │   └── walkthrough.md
│   ├── ranking_dashboard
│   │   ├── dashboard_report.md
│   │   ├── demo_checklist.md
│   │   ├── demo_script.md
│   │   ├── module_summary.md
│   │   ├── notification_report.md
│   │   ├── ranking_bug_list.md
│   │   ├── ranking_report.md
│   │   ├── ranking_test_final.md
│   │   ├── ranking_test_result.md
│   │   ├── ranking_verification.md
│   │   ├── ranking_verification_final.md
│   │   └── walkthrough.md
│   ├── series
│   │   ├── chapter_report.md
│   │   ├── module_summary.md
│   │   ├── page_report.md
│   │   ├── series_bug_list.md
│   │   ├── series_report.md
│   │   ├── series_test_final.md
│   │   ├── series_test_result.md
│   │   ├── series_verification.md
│   │   ├── series_verification_final.md
│   │   └── walkthrough.md
│   ├── task_submission
│   │   ├── module_summary.md
│   │   ├── submission_report.md
│   │   ├── task_bug_list.md
│   │   ├── task_report.md
│   │   ├── task_test_final.md
│   │   ├── task_test_result.md
│   │   ├── task_verification.md
│   │   ├── task_verification_final.md
│   │   └── walkthrough.md
│   ├── testing
│   │   ├── authentication_test_result.md
│   │   ├── authentication_verification.md
│   │   ├── bug_list.md
│   │   ├── e2e_logical_validation_report.md
│   │   ├── e2e_report.md
│   │   ├── mangaka_logic_test.md
│   │   ├── permission_matrix.md
│   │   ├── regression_bug_list.md
│   │   ├── regression_report.md
│   │   ├── regression_test.md
│   │   ├── security_report.md
│   │   ├── security_test.md
│   │   └── test_cases.md
│   ├── Database_design.md
│   ├── DinhHuongPhatTrien.md
│   ├── MVC.md
│   ├── PROJECT_PLAN.md
│   ├── Project_Checklist.md
│   ├── TaiLieuDacTa.md
│   ├── User_Manual.md
│   ├── business_logic_guideline.md
│   ├── codebase_explanation.md
│   ├── codebase_explanation_full.pdf
│   ├── data_access_defense.md
│   ├── he_thong_trang_thai_va_vong_doi.md
│   ├── huong_dan_tinh_diem_xep_hang.pdf
│   ├── installation_and_architecture.md
│   ├── multi_user_workflow_guide.md
│   ├── presentation_and_demo_script.pdf
│   ├── technology_stack.md
│   ├── uml_real_code_alignment_report.md
│   ├── use_case_description.md
│   ├── workflow_audit.md
│   └── workflow_multi_role_analysis.md
├── models
│   ├── BoardVote.php
│   ├── Chapter.php
│   ├── EditorAnnotation.php
│   ├── Notification.php
│   ├── Page.php
│   ├── PageRegion.php
│   ├── Review.php
│   ├── Role.php
│   ├── Series.php
│   ├── SeriesRanking.php
│   ├── Submission.php
│   ├── SubmissionAnnotation.php
│   ├── SystemLog.php
│   ├── Task.php
│   └── User.php
├── uploads
│   ├── covers
│   │   ├── cover_6a4bc43b8c9e8.png
│   │   ├── cover_6a4bc54455bba.png
│   │   ├── cover_6a4bc630ddbff.png
│   │   ├── cover_6a4cad949f639.png
│   │   ├── cover_6a4cb343c3cf4.png
│   │   ├── cover_6a4dbfe97e515.png
│   │   ├── cover_6a4e35469950a.png
│   │   ├── cover_6a4e532878d32.png
│   │   ├── cover_6a4e6d4044a61.png
│   │   ├── cover_6a4e6ecbeedc1.png
│   │   ├── cover_6a4e817072e6c.png
│   │   ├── cover_6a4e98680e006.png
│   │   ├── cover_6a539f6d6f730.png
│   │   ├── cover_6a53a8b67d40b.jpg
│   │   ├── cover_6a5526e644c59.png
│   │   └── cover_6a55bb6db9738.png
│   ├── extracted
│   │   ├── sub_2
│   │   │   └── Ban_Phac_Thao_Tong_Hop
│   │   │       ├── CHAPTER1_PAGE_1.png
│   │   │       ├── CHAPTER1_PAGE_2.png
│   │   │       └── bia_Chapter.png
│   │   ├── sub_3
│   │   │   └── Ban_Phac_Thao_Tong_Hop
│   │   │       ├── CHAPTER1_PAGE_1.png
│   │   │       ├── CHAPTER1_PAGE_2.png
│   │   │       └── bia_Chapter.png
│   │   ├── sub_3_d4c34c6f864dffcf32cea2cf2f717c09
│   │   │   └── Ban_Phac_Thao_Tong_Hop
│   │   │       ├── CHAPTER1_PAGE_1.png
│   │   │       ├── CHAPTER1_PAGE_2.png
│   │   │       └── bia_Chapter.png
│   │   ├── sub_5_06cb4ba9c3f7aef11729d9e53eabce04
│   │   │   └── Ban_Phac_Thao_Tong_Hop
│   │   │       ├── CHAPTER1_PAGE_1.png
│   │   │       ├── CHAPTER1_PAGE_2.png
│   │   │       └── bia_Chapter.png
│   │   └── test_extract_1784006552
│   │       └── Ban_Phac_Thao_Tong_Hop
│   │           ├── CHAPTER1_PAGE_1.png
│   │           ├── CHAPTER1_PAGE_2.png
│   │           └── bia_Chapter.png
│   ├── pages
│   │   ├── page_6a4bc6e7374e8.png
│   │   ├── page_6a4bc6f937815.png
│   │   ├── page_6a4cb8c3c09cb.png
│   │   ├── page_6a4cb9a69ba25.jpg
│   │   ├── page_6a4dc286aa0ad.png
│   │   ├── page_6a4dc29fad011.png
│   │   ├── page_6a4e3979e2d2c.png
│   │   ├── page_6a4e3cdc09f64.png
│   │   ├── page_6a4e3d392ffa5.png
│   │   ├── page_6a4e442f64c25.png
│   │   ├── page_6a4e56603220f.png
│   │   ├── page_6a4e63eab7ba1.png
│   │   ├── page_6a4e63ff17d8d.jpg
│   │   ├── page_6a4e6429c5d6b.jpg
│   │   ├── page_6a4e6e21c193f.jpg
│   │   ├── page_6a4e6f2ea9550.png
│   │   ├── page_6a4e81eba11ac.png
│   │   ├── page_6a4e81f810930.png
│   │   ├── page_6a4e9aedf0347.png
│   │   ├── page_6a4e9b1815a94.png
│   │   ├── page_6a53a50671e28.png
│   │   ├── page_6a53a520aae99.jpg
│   │   ├── page_6a5527e5717f4.jpg
│   │   ├── page_6a55c1b2c0821.png
│   │   ├── page_6a55c2136f0f6.png
│   │   ├── page_6a55ed83d6dac.png
│   │   └── page_6a55ed9eb17af.jpg
│   ├── proposals
│   │   ├── proposal_6a4cad94a022a.docx
│   │   ├── proposal_6a4cb343c41aa.docx
│   │   ├── proposal_6a4dbfe97e819.docx
│   │   ├── proposal_6a4e354699985.docx
│   │   ├── proposal_6a4e5328798d1.docx
│   │   ├── proposal_6a4e6d404505a.docx
│   │   ├── proposal_6a4e6ecbef40a.docx
│   │   ├── proposal_6a4e8170738a3.docx
│   │   ├── proposal_6a4e98680e6c2.docx
│   │   ├── proposal_6a539f6d702f1.docx
│   │   ├── proposal_6a53a8b67d60d.docx
│   │   ├── proposal_6a5526e6450c8.docx
│   │   └── proposal_6a55bb6db9ccc.docx
│   └── submissions
│       ├── 1783351326_CHAPTER1_PAGE_1.png
│       ├── 1783351506_CHAPTER1_PAGE_1.png
│       ├── 1783418014_demo_boi_canh.jpg
│       ├── 1783419354_demo_nhanvat.jpg
│       ├── 1783419387_demo_bongchat.jpg
│       ├── 1783419834_demo_bongchat.jpg
│       ├── 1783419984_bia_Chapter.png
│       ├── 1783481396_DANH_SACH_TIEU_LUAN__1_.pdf
│       ├── 1783512596_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783514959_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783519149_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783520945_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783521935_demo_boi_canh.jpg
│       ├── 1783522375_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783524911_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783525179_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783529991_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783535917_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783536143_bia_Chapter.png
│       ├── 1783536446_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783865567_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783865879_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783866385_DEMO-THUCHIENCUATROLY-BOICANH.png
│       ├── 1783866420_DEMO-THUCHIENCUATROLY-BONGTHOAI.png
│       ├── 1783866452_DEMO-THUCHIENCUATROLY-NHANVAT.png
│       ├── 1783866693_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1783965722_ANH_BIA_CHAPTER-BANHOANCHINH-CUOICUNG.jpg
│       ├── 1784003797_Ban_Phac_Thao_Tong_Hop.zip
│       ├── 1784005200_Ban_Phac_Thao_Tong_Hop_-_Sao_ch__p.zip
│       ├── 1784013929_z8016747972561_0076065aa83768e0f0777bbbbf4717b9.jpg
│       └── 1784016322_Ban_Phac_Thao_Tong_Hop.zip
├── views
│   ├── admin
│   │   ├── dashboard.php
│   │   ├── logs.php
│   │   ├── roles.php
│   │   ├── user_create.php
│   │   ├── user_detail.php
│   │   ├── user_edit.php
│   │   └── users.php
│   ├── assistant
│   │   ├── dashboard.php
│   │   ├── task_list.php
│   │   └── upload_submission.php
│   ├── auth
│   │   └── login.php
│   ├── board
│   │   ├── dashboard.php
│   │   ├── publish_series.php
│   │   ├── ranking_create.php
│   │   ├── ranking_detail.php
│   │   ├── ranking_edit.php
│   │   └── rankings.php
│   ├── editor
│   │   ├── dashboard.php
│   │   ├── dossier_detail.php
│   │   ├── dossiers.php
│   │   ├── progress.php
│   │   ├── review_create.php
│   │   ├── review_detail.php
│   │   ├── review_list.php
│   │   ├── submission_detail.php
│   │   └── submission_list.php
│   ├── layouts
│   │   ├── alerts.php
│   │   ├── footer.php
│   │   ├── header.php
│   │   ├── navbar.php
│   │   ├── sidebar.php
│   │   └── welcome_banner.php
│   ├── mangaka
│   │   ├── chapter_create.php
│   │   ├── chapter_detail.php
│   │   ├── chapter_edit.php
│   │   ├── chapter_list.php
│   │   ├── dashboard.php
│   │   ├── page_create.php
│   │   ├── page_detail.php
│   │   ├── page_edit.php
│   │   ├── page_list.php
│   │   ├── rankings.php
│   │   ├── series.php
│   │   ├── series_create.php
│   │   ├── series_detail.php
│   │   ├── series_edit.php
│   │   ├── submission_create.php
│   │   ├── task_create.php
│   │   ├── task_edit.php
│   │   └── task_list.php
│   └── shared
│       ├── dashboard_notifications.php
│       ├── notifications.php
│       └── profile.php
├── .dockerignore
├── .gitignore
├── README.md
├── docker-compose.yml
├── fix_db.php
└── index.php
`
