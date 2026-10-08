<?php

namespace Database\Seeders;

use App\Enums\AttemptType;
use App\Enums\SessionStatus;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Application;
use App\Models\ClinicalFacility;
use App\Models\ClinicalLogbook;
use App\Models\ClinicalPosting;
use App\Models\ClinicalProcedure;
use App\Models\ClinicalWard;
use App\Models\CompetencyCategory;
use App\Models\CompetencySkill;
use App\Models\Course;
use App\Models\Department;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\FinancialClearance;
use App\Models\Invoice;
use App\Models\Level;
use App\Models\OsceExamination;
use App\Models\OsceStation;
use App\Models\OsceStationRubric;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Programme;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentCompetency;
use App\Models\StudentCourseRegistration;
use App\Models\StudentCourseRegistrationItem;
use App\Models\StudentResult;
use App\Models\StudentSemesterResult;
use App\Models\User;
use App\Services\Academics\AcademicSessionTransitionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles Definition
        $rolesData = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'registrar' => 'Registrar',
            'dean' => 'Dean',
            'hod' => 'Head of Department',
            'academic_officer' => 'Academic Officer',
            'lecturer' => 'Academic Lecturer',
            'clinical_coordinator' => 'Clinical Coordinator',
            'clinical_instructor' => 'Clinical Preceptor/Instructor',
            'exam_officer' => 'Examination Officer',
            'finance_officer' => 'Finance Officer',
            'bursar' => 'Bursar',
            'student' => 'Student Nurse/Midwife',
            'applicant' => 'Prospective Applicant',
        ];

        $roleModels = [];
        foreach ($rolesData as $name => $displayName) {
            $roleModels[$name] = Role::firstOrCreate(['name' => $name], ['display_name' => $displayName]);
        }

        // 2. Comprehensive Modular Permissions Definition
        $permissionsList = [
            // Users & RBAC
            'users.view' => ['View Users', 'users'],
            'users.create' => ['Create Users', 'users'],
            'users.update' => ['Update Users', 'users'],
            'users.delete' => ['Delete Users', 'users'],
            'users.activate' => ['Activate Users', 'users'],
            'users.deactivate' => ['Deactivate Users', 'users'],
            'users.reset_password' => ['Reset Passwords', 'users'],
            'users.impersonate' => ['Impersonate Users', 'users'],
            'roles.view' => ['View Roles', 'roles'],
            'roles.create' => ['Create Roles', 'roles'],
            'roles.update' => ['Update Roles', 'roles'],
            'roles.delete' => ['Delete Roles', 'roles'],
            'roles.assign' => ['Assign Roles', 'roles'],
            'permissions.view' => ['View Permissions', 'permissions'],
            'permissions.create' => ['Create Permissions', 'permissions'],
            'permissions.update' => ['Update Permissions', 'permissions'],
            'permissions.delete' => ['Delete Permissions', 'permissions'],
            'permissions.assign' => ['Assign Permissions', 'permissions'],

            // Admissions
            'admissions.view' => ['View Applications', 'admissions'],
            'admissions.create' => ['Create Applications', 'admissions'],
            'admissions.update' => ['Update Applications', 'admissions'],
            'admissions.delete' => ['Delete Applications', 'admissions'],
            'admissions.screen' => ['Screen & Score Applicants', 'admissions'],
            'admissions.shortlist' => ['Shortlist Applicants', 'admissions'],
            'admissions.interview' => ['Interview Applicants', 'admissions'],
            'admissions.approve' => ['Approve/Admit Applicants', 'admissions'],
            'admissions.reject' => ['Reject Applicants', 'admissions'],
            'admissions.reopen' => ['Reopen Applications', 'admissions'],
            'admissions.generate_letter' => ['Generate Admission Letters', 'admissions'],
            'admissions.publish' => ['Publish Admission List', 'admissions'],

            // Students
            'students.view' => ['View Student Records', 'students'],
            'students.create' => ['Create Student Records', 'students'],
            'students.update' => ['Update Student Records', 'students'],
            'students.delete' => ['Delete Student Records', 'students'],
            'students.activate' => ['Activate Students', 'students'],
            'students.deactivate' => ['Deactivate Students', 'students'],
            'students.suspend' => ['Suspend Students', 'students'],
            'students.promote' => ['Promote Students', 'students'],
            'students.view_academic' => ['View Academic Records', 'students'],
            'students.view_financial' => ['View Financial Records', 'students'],
            'students.view_clinical' => ['View Clinical Records', 'students'],
            'students.view_documents' => ['View Student Documents', 'students'],
            'students.view_own' => ['View Own Record', 'students'],
            'students.update_own' => ['Update Own Record', 'students'],
            'students.view_own_results' => ['View Own Results', 'students'],
            'students.view_own_finance' => ['View Own Finance', 'students'],
            'students.view_own_clinical' => ['View Own Clinical', 'students'],

            // Departments & Programmes
            'departments.view' => ['View Departments', 'departments'],
            'departments.create' => ['Create Departments', 'departments'],
            'departments.update' => ['Update Departments', 'departments'],
            'departments.delete' => ['Delete Departments', 'departments'],
            'programmes.view' => ['View Programmes', 'programmes'],
            'programmes.create' => ['Create Programmes', 'programmes'],
            'programmes.update' => ['Update Programmes', 'programmes'],
            'programmes.delete' => ['Delete Programmes', 'programmes'],
            'programmes.activate' => ['Activate Programmes', 'programmes'],
            'programmes.deactivate' => ['Deactivate Programmes', 'programmes'],

            // Courses & Curriculum
            'courses.view' => ['View Courses', 'courses'],
            'courses.create' => ['Create Courses', 'courses'],
            'courses.update' => ['Update Courses', 'courses'],
            'courses.delete' => ['Delete Courses', 'courses'],
            'courses.manage' => ['Manage Courses', 'courses'],
            'courses.assign_lecturer' => ['Assign Lecturers', 'courses'],
            'courses.assign_students' => ['Assign Students', 'courses'],
            'courses.publish' => ['Publish Courses', 'courses'],
            'courses.view_assigned' => ['View Assigned Courses', 'courses'],
            'curriculum.view' => ['View Curriculum', 'curriculum'],
            'curriculum.create' => ['Create Curriculum', 'curriculum'],
            'curriculum.update' => ['Update Curriculum', 'curriculum'],
            'curriculum.delete' => ['Delete Curriculum', 'curriculum'],
            'curriculum.add_course' => ['Add Course to Curriculum', 'curriculum'],
            'curriculum.remove_course' => ['Remove Course from Curriculum', 'curriculum'],
            'curriculum.approve' => ['Approve Curriculum', 'curriculum'],
            'curriculum.publish' => ['Publish Curriculum', 'curriculum'],

            // Course Registration
            'registration.view' => ['View Course Registrations', 'registration'],
            'registration.open' => ['Open Course Registration', 'registration'],
            'registration.close' => ['Close Course Registration', 'registration'],
            'registration.register' => ['Register Courses', 'registration'],
            'registration.update' => ['Update Registration', 'registration'],
            'registration.cancel' => ['Cancel Registration', 'registration'],
            'registration.approve' => ['Approve Registration', 'registration'],
            'registration.reject' => ['Reject Registration', 'registration'],
            'registration.override' => ['Override Registration Rules', 'registration'],

            // Academic Sessions
            'sessions.view' => ['View Academic Sessions', 'sessions'],
            'sessions.create' => ['Create Academic Sessions', 'sessions'],
            'sessions.update' => ['Update Academic Sessions', 'sessions'],
            'sessions.activate' => ['Activate Academic Sessions', 'sessions'],
            'sessions.begin_result_processing' => ['Begin Result Processing for Sessions', 'sessions'],
            'sessions.close' => ['Close Academic Sessions', 'sessions'],
            'sessions.archive' => ['Archive Academic Sessions', 'sessions'],
            'sessions.override' => ['Administrative Override for Sessions', 'sessions'],

            // Attendance
            'attendance.view' => ['View Attendance', 'attendance'],
            'attendance.create' => ['Record Attendance', 'attendance'],
            'attendance.update' => ['Update Attendance', 'attendance'],
            'attendance.delete' => ['Delete Attendance', 'attendance'],
            'attendance.lock' => ['Lock Attendance', 'attendance'],
            'attendance.unlock' => ['Unlock Attendance', 'attendance'],
            'attendance.export' => ['Export Attendance', 'attendance'],
            'attendance.view_assigned' => ['View Assigned Attendance', 'attendance'],

            // Assessments & Scores
            'assessments.view' => ['View Assessments', 'assessments'],
            'assessments.create' => ['Create Assessments', 'assessments'],
            'assessments.update' => ['Update Assessments', 'assessments'],
            'assessments.delete' => ['Delete Assessments', 'assessments'],
            'assessment_scores.enter' => ['Enter Assessment Scores', 'assessments'],
            'assessment_scores.update' => ['Update Assessment Scores', 'assessments'],
            'assessment_scores.submit' => ['Submit Assessment Scores', 'assessments'],
            'assessment_scores.lock' => ['Lock Assessment Scores', 'assessments'],

            // Examinations
            'examinations.view' => ['View Examinations', 'examinations'],
            'examinations.create' => ['Create Examinations', 'examinations'],
            'examinations.update' => ['Update Examinations', 'examinations'],
            'examinations.delete' => ['Delete Examinations', 'examinations'],
            'examinations.schedule' => ['Schedule Examinations', 'examinations'],
            'examinations.assign_invigilator' => ['Assign Invigilators', 'examinations'],
            'examinations.generate_attendance' => ['Generate Exam Attendance', 'examinations'],
            'examinations.mark_attendance' => ['Mark Exam Attendance', 'examinations'],
            'examinations.record_malpractice' => ['Record Exam Malpractice', 'examinations'],

            // Results Workflow (4-Layer Control)
            'results.view' => ['View Results', 'results'],
            'results.enter' => ['Enter Assessment Scores', 'results'],
            'results.update' => ['Update Results', 'results'],
            'results.submit' => ['Submit Results', 'results'],
            'results.verify' => ['Verify Results', 'results'],
            'results.reject' => ['Reject Results', 'results'],
            'results.approve' => ['Approve Results', 'results'],
            'results.reject_approval' => ['Reject Result Approval', 'results'],
            'results.publish' => ['Publish Examination Results', 'results'],
            'results.unpublish' => ['Unpublish Results', 'results'],
            'results.reopen' => ['Reopen Results for Correction', 'results'],
            'results.export' => ['Export Results Broadsheet', 'results'],

            // Clinical
            'clinical.view' => ['View Clinical Postings', 'clinical'],
            'clinical.create' => ['Create Clinical Rotations', 'clinical'],
            'clinical.update' => ['Update Clinical Rotations', 'clinical'],
            'clinical.delete' => ['Delete Clinical Rotations', 'clinical'],
            'clinical.manage' => ['Manage Hospital Rotations', 'clinical'],
            'clinical.evaluate' => ['Sign Off Clinical Logbook', 'clinical'],
            'clinical.facilities.manage' => ['Manage Hospitals/Facilities', 'clinical'],
            'clinical.units.manage' => ['Manage Hospital Wards/Units', 'clinical'],
            'clinical.groups.create' => ['Create Clinical Groups', 'clinical'],
            'clinical.groups.update' => ['Update Clinical Groups', 'clinical'],
            'clinical.postings.create' => ['Create Postings', 'clinical'],
            'clinical.postings.update' => ['Update Postings', 'clinical'],
            'clinical.postings.cancel' => ['Cancel Postings', 'clinical'],
            'clinical.postings.approve' => ['Approve Postings', 'clinical'],
            'clinical.attendance.view' => ['View Clinical Attendance', 'clinical'],
            'clinical.attendance.record' => ['Record Clinical Attendance', 'clinical'],
            'clinical.attendance.update' => ['Update Clinical Attendance', 'clinical'],
            'clinical.logbook.view' => ['View Clinical Logbooks', 'clinical'],
            'clinical.logbook.review' => ['Review Clinical Logbooks', 'clinical'],
            'clinical.logbook.approve' => ['Approve/Sign Off Logbooks', 'clinical'],
            'clinical.competency.view' => ['View Clinical Competencies', 'clinical'],
            'clinical.competency.assess' => ['Assess Clinical Competencies', 'clinical'],
            'clinical.competency.approve' => ['Approve Competency Levels', 'clinical'],

            // OSCE
            'osce.view' => ['View OSCE Exams', 'osce'],
            'osce.create' => ['Create OSCE Exams', 'osce'],
            'osce.update' => ['Update OSCE Exams', 'osce'],
            'osce.delete' => ['Delete OSCE Exams', 'osce'],
            'osce.conduct' => ['Conduct OSCE Assessments', 'osce'],
            'osce.create_station' => ['Create OSCE Station', 'osce'],
            'osce.assign_assessor' => ['Assign OSCE Assessor', 'osce'],
            'osce.assign_candidate' => ['Assign OSCE Candidate', 'osce'],
            'osce.score' => ['Score OSCE Candidate', 'osce'],
            'osce.submit_score' => ['Submit OSCE Scores', 'osce'],
            'osce.verify' => ['Verify OSCE Station Scores', 'osce'],
            'osce.approve' => ['Approve OSCE Results', 'osce'],
            'osce.publish' => ['Publish OSCE Results', 'osce'],

            // Finance & Clearance
            'finance.dashboard' => ['View Finance Dashboard', 'finance'],
            'finance.view' => ['View Financial Records', 'finance'],
            'finance.create' => ['Create Financial Entries', 'finance'],
            'finance.update' => ['Update Financial Entries', 'finance'],
            'finance.manage' => ['Manage Fees & Invoices', 'finance'],
            'finance.verify' => ['Verify Payments & Grant Clearance', 'finance'],
            'fees.create' => ['Create Fee Structures', 'finance'],
            'fees.update' => ['Update Fee Structures', 'finance'],
            'fees.delete' => ['Delete Fee Structures', 'finance'],
            'fees.publish' => ['Publish Fee Schedule', 'finance'],
            'invoices.view' => ['View Invoices', 'finance'],
            'invoices.create' => ['Generate Invoices', 'finance'],
            'invoices.update' => ['Update Invoices', 'finance'],
            'payments.view' => ['View Payments', 'finance'],
            'payments.verify' => ['Verify Payments', 'finance'],
            'payments.reverse' => ['Reverse Payments', 'finance'],
            'receipts.view' => ['View Receipts', 'finance'],
            'receipts.generate' => ['Generate Official Receipts', 'finance'],
            'clearance.view' => ['View Clearance Status', 'finance'],
            'clearance.approve' => ['Approve Financial Clearance', 'finance'],
            'clearance.reject' => ['Reject Financial Clearance', 'finance'],
            'clearance.override' => ['Override Financial Clearance', 'finance'],

            // Staff Management
            'staff.view' => ['View Staff Directory', 'staff'],
            'staff.create' => ['Create Staff Records', 'staff'],
            'staff.update' => ['Update Staff Records', 'staff'],
            'staff.delete' => ['Delete Staff Records', 'staff'],
            'staff.assign_department' => ['Assign Staff to Department', 'staff'],
            'staff.assign_course' => ['Assign Staff to Course', 'staff'],
            'staff.assign_clinical_role' => ['Assign Clinical Supervisory Role', 'staff'],
            'staff.activate' => ['Activate Staff', 'staff'],
            'staff.deactivate' => ['Deactivate Staff', 'staff'],

            // Reports
            'reports.view' => ['View Reports', 'reports'],
            'reports.academic' => ['Academic Reports', 'reports'],
            'reports.admission' => ['Admission Reports', 'reports'],
            'reports.finance' => ['Finance Reports', 'reports'],
            'reports.clinical' => ['Clinical Reports', 'reports'],
            'reports.examination' => ['Examination Reports', 'reports'],
            'reports.student' => ['Student Demographics Reports', 'reports'],
            'reports.staff' => ['Staff Workload Reports', 'reports'],
            'reports.export' => ['Export Reports', 'reports'],
            'reports.export_sensitive' => ['Export Sensitive Data', 'reports'],

            // Student Portal
            'student.dashboard' => ['Access Student Dashboard', 'student_portal'],
            'student.profile.view' => ['View Own Profile', 'student_portal'],
            'student.profile.update' => ['Update Own Profile', 'student_portal'],
            'student.registration.view' => ['View Course Offerings', 'student_portal'],
            'student.registration.create' => ['Submit Course Registration', 'student_portal'],
            'student.registration.update' => ['Modify Course Registration', 'student_portal'],
            'student.timetable.view' => ['View Academic Timetable', 'student_portal'],
            'student.results.view' => ['View Examination Result Slip', 'student_portal'],
            'student.transcript.request' => ['Request Transcript', 'student_portal'],
            'student.fees.view' => ['View Invoices & Balances', 'student_portal'],
            'student.payments.view' => ['View Payment History', 'student_portal'],
            'student.receipts.view' => ['Download Official Receipts', 'student_portal'],
            'student.clinical.view' => ['View Hospital Postings', 'student_portal'],
            'student.logbook.view' => ['View Personal Logbook Entries', 'student_portal'],
            'student.logbook.submit' => ['Submit Digital Logbook Entries', 'student_portal'],
            'student.documents.view' => ['View Uploaded Documents', 'student_portal'],
            'student.documents.download' => ['Download Documents', 'student_portal'],
            'student.notifications.view' => ['View Notifications', 'student_portal'],

            // Applicant Portal
            'application.create' => ['Create Application', 'applicant_portal'],
            'application.view_own' => ['View Own Application', 'applicant_portal'],
            'application.update_own' => ['Update Own Application', 'applicant_portal'],
            'application.submit' => ['Submit Application', 'applicant_portal'],
            'application.upload_document' => ['Upload Credentials', 'applicant_portal'],
            'application.make_payment' => ['Pay Application / Acceptance Fees', 'applicant_portal'],
            'application.view_status' => ['View Admission Decision', 'applicant_portal'],
            'application.download_letter' => ['Download Admission Letter', 'applicant_portal'],

            // Audit
            'audit.view' => ['View Institutional Audit Logs', 'audit'],
            'audit.export' => ['Export Audit Logs', 'audit'],
        ];

        $permissionModels = [];
        foreach ($permissionsList as $name => [$displayName, $group]) {
            $permissionModels[$name] = Permission::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName, 'group' => $group]
            );
        }

        // 3. Matrix Mapping (ChatGPT ACL Specification)
        $rolePermissionsMap = [
            'super_admin' => array_keys($permissionsList),
            'admin' => array_filter(array_keys($permissionsList), fn ($p) => ! str_starts_with($p, 'student.') && ! str_starts_with($p, 'application.')),
            'registrar' => [
                'admissions.view', 'admissions.create', 'admissions.update', 'admissions.screen',
                'admissions.shortlist', 'admissions.approve', 'admissions.reject', 'admissions.reopen',
                'admissions.generate_letter', 'admissions.publish',
                'students.view', 'students.create', 'students.update', 'students.activate',
                'students.promote', 'students.view_academic', 'students.view_documents',
                'departments.view', 'programmes.view', 'courses.view', 'curriculum.view', 'curriculum.approve',
                'registration.view', 'registration.open', 'registration.close', 'registration.approve', 'registration.override',
                'results.view', 'results.approve', 'results.export',
                'clearance.view', 'clearance.override',
                'sessions.view', 'sessions.create', 'sessions.update', 'sessions.activate', 'sessions.begin_result_processing', 'sessions.close', 'sessions.archive', 'sessions.override',
                'reports.view', 'reports.academic', 'reports.admission', 'reports.student', 'reports.export',
                'audit.view',
            ],
            'dean' => [
                'admissions.view', 'admissions.screen',
                'students.view', 'students.view_academic', 'students.view_clinical',
                'departments.view', 'programmes.view', 'courses.view', 'curriculum.view', 'curriculum.approve',
                'registration.view', 'registration.override',
                'results.view', 'results.approve', 'results.export',
                'clinical.view', 'osce.view', 'osce.approve',
                'reports.view', 'reports.academic', 'reports.clinical', 'reports.examination', 'reports.export',
                'audit.view',
            ],
            'hod' => [
                'admissions.view', 'admissions.screen',
                'students.view', 'students.view_academic', 'students.view_clinical',
                'departments.view', 'programmes.view', 'courses.view', 'courses.manage', 'courses.assign_lecturer', 'courses.publish',
                'curriculum.view', 'curriculum.update', 'curriculum.add_course', 'curriculum.remove_course',
                'registration.view', 'registration.approve', 'registration.reject',
                'sessions.view',
                'attendance.view', 'attendance.export',
                'assessments.view', 'assessment_scores.enter', 'assessment_scores.update',
                'results.view', 'results.enter', 'results.verify', 'results.reject', 'results.export',
                'clinical.view', 'clinical.logbook.view', 'clinical.logbook.review', 'clinical.logbook.approve',
                'osce.view', 'osce.score', 'osce.verify',
                'reports.view', 'reports.academic', 'reports.clinical', 'reports.export',
            ],
            'academic_officer' => [
                'admissions.view', 'admissions.screen',
                'students.view', 'students.view_academic',
                'programmes.view', 'programmes.create', 'programmes.update',
                'courses.view', 'courses.create', 'courses.update', 'courses.manage', 'courses.assign_lecturer', 'courses.assign_students',
                'curriculum.view', 'curriculum.create', 'curriculum.update', 'curriculum.add_course', 'curriculum.remove_course',
                'registration.view', 'registration.open', 'registration.close', 'registration.approve', 'registration.override',
                'sessions.view', 'sessions.create', 'sessions.update', 'sessions.activate', 'sessions.begin_result_processing', 'sessions.close',
                'reports.view', 'reports.academic', 'reports.export',
            ],
            'lecturer' => [
                'courses.view', 'courses.view_assigned',
                'attendance.view', 'attendance.create', 'attendance.update', 'attendance.view_assigned',
                'assessments.view', 'assessments.create', 'assessments.update', 'assessment_scores.enter', 'assessment_scores.update', 'assessment_scores.submit',
                'results.view', 'results.enter', 'results.update', 'results.submit',
                'osce.view', 'osce.score', 'osce.submit_score',
                'students.view', 'students.view_academic',
            ],
            'clinical_coordinator' => [
                'clinical.view', 'clinical.create', 'clinical.update', 'clinical.delete', 'clinical.manage',
                'clinical.facilities.manage', 'clinical.units.manage', 'clinical.groups.create', 'clinical.groups.update',
                'clinical.postings.create', 'clinical.postings.update', 'clinical.postings.cancel', 'clinical.postings.approve',
                'clinical.attendance.view', 'clinical.attendance.record', 'clinical.attendance.update',
                'clinical.logbook.view', 'clinical.logbook.review', 'clinical.logbook.approve',
                'clinical.competency.view', 'clinical.competency.assess', 'clinical.competency.approve',
                'osce.view', 'osce.create', 'osce.create_station', 'osce.assign_assessor', 'osce.assign_candidate', 'osce.verify',
                'students.view', 'students.view_clinical',
                'reports.view', 'reports.clinical', 'reports.export',
            ],
            'clinical_instructor' => [
                'clinical.view', 'clinical.evaluate',
                'clinical.attendance.view', 'clinical.attendance.record', 'clinical.attendance.update',
                'clinical.logbook.view', 'clinical.logbook.review', 'clinical.logbook.approve',
                'clinical.competency.view', 'clinical.competency.assess', 'clinical.competency.approve',
                'osce.view', 'osce.score', 'osce.submit_score',
                'students.view', 'students.view_clinical',
            ],
            'exam_officer' => [
                'examinations.view', 'examinations.create', 'examinations.update', 'examinations.schedule',
                'examinations.assign_invigilator', 'examinations.generate_attendance', 'examinations.mark_attendance', 'examinations.record_malpractice',
                'results.view', 'results.verify', 'results.approve', 'results.publish', 'results.unpublish', 'results.reopen', 'results.export',
                'osce.view', 'osce.verify', 'osce.approve', 'osce.publish',
                'reports.view', 'reports.examination', 'reports.export',
            ],
            'finance_officer' => [
                'finance.dashboard', 'finance.view', 'finance.create', 'finance.update', 'finance.manage', 'finance.verify',
                'fees.create', 'fees.update', 'fees.publish',
                'invoices.view', 'invoices.create', 'invoices.update',
                'payments.view', 'payments.verify',
                'receipts.view', 'receipts.generate',
                'clearance.view', 'clearance.approve', 'clearance.reject',
                'students.view', 'students.view_financial',
                'reports.view', 'reports.finance', 'reports.export',
            ],
            'bursar' => [
                'finance.dashboard', 'finance.view', 'finance.create', 'finance.update', 'finance.manage', 'finance.verify',
                'fees.create', 'fees.update', 'fees.delete', 'fees.publish',
                'invoices.view', 'invoices.create', 'invoices.update',
                'payments.view', 'payments.verify', 'payments.reverse',
                'receipts.view', 'receipts.generate',
                'clearance.view', 'clearance.approve', 'clearance.reject', 'clearance.override',
                'students.view', 'students.view_financial',
                'reports.view', 'reports.finance', 'reports.export', 'reports.export_sensitive',
            ],
            'student' => [
                'student.dashboard', 'student.profile.view', 'student.profile.update',
                'student.registration.view', 'student.registration.create', 'student.registration.update',
                'student.timetable.view', 'student.results.view', 'student.transcript.request',
                'student.fees.view', 'student.payments.view', 'student.receipts.view',
                'student.clinical.view', 'student.logbook.view', 'student.logbook.submit',
                'student.documents.view', 'student.documents.download', 'student.notifications.view',
                'students.view_own', 'students.update_own', 'students.view_own_results', 'students.view_own_finance', 'students.view_own_clinical',
            ],
            'applicant' => [
                'application.create', 'application.view_own', 'application.update_own', 'application.submit',
                'application.upload_document', 'application.make_payment', 'application.view_status', 'application.download_letter',
            ],
        ];

        foreach ($rolePermissionsMap as $roleName => $perms) {
            $role = $roleModels[$roleName] ?? null;
            if ($role) {
                $ids = [];
                foreach ($perms as $pName) {
                    if (isset($permissionModels[$pName])) {
                        $ids[] = $permissionModels[$pName]->id;
                    }
                }
                $role->permissions()->sync($ids);
            }
        }

        // 4. Staff & Institutional Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@cnims.edu.ng'],
            ['name' => 'Prof. Fatima Bello', 'password' => Hash::make('password123')]
        );
        $adminUser->assignRole('super_admin');

        $instAdminUser = User::firstOrCreate(
            ['email' => 'institutional.admin@cnims.edu.ng'],
            ['name' => 'Dr. Aliyu Mohammed (Institutional Admin)', 'password' => Hash::make('password123')]
        );
        $instAdminUser->assignRole('admin');

        $registrarUser = User::firstOrCreate(
            ['email' => 'registrar@cnims.edu.ng'],
            ['name' => 'Dr. Amina Abubakar (Registrar)', 'password' => Hash::make('password123')]
        );
        $registrarUser->assignRole('registrar');

        $deanUser = User::firstOrCreate(
            ['email' => 'dean@cnims.edu.ng'],
            ['name' => 'Prof. Sani Garba (Dean)', 'password' => Hash::make('password123')]
        );
        $deanUser->assignRole('dean');

        $hodUser = User::firstOrCreate(
            ['email' => 'hod.nursing@cnims.edu.ng'],
            ['name' => 'Dr. Emmanuel Adeyemi (HOD)', 'password' => Hash::make('password123')]
        );
        $hodUser->assignRole('hod');

        $academicOfficer = User::firstOrCreate(
            ['email' => 'academic.officer@cnims.edu.ng'],
            ['name' => 'Mr. Victor Olawale (Academic Officer)', 'password' => Hash::make('password123')]
        );
        $academicOfficer->assignRole('academic_officer');

        $lecturerUser = User::firstOrCreate(
            ['email' => 'lecturer@cnims.edu.ng'],
            ['name' => 'Mrs. Grace Danladi (RN, RM, MSc)', 'password' => Hash::make('password123')]
        );
        $lecturerUser->assignRole('lecturer');

        $clinicalCoordinator = User::firstOrCreate(
            ['email' => 'clinical.coordinator@cnims.edu.ng'],
            ['name' => 'Nurse Hauwa Sambo (Clinical Coordinator)', 'password' => Hash::make('password123')]
        );
        $clinicalCoordinator->assignRole('clinical_coordinator');

        $clinicalInstructor = User::firstOrCreate(
            ['email' => 'clinical.instructor@cnims.edu.ng'],
            ['name' => 'Nurse Ibrahim Yakubu (Clinical Preceptor)', 'password' => Hash::make('password123')]
        );
        $clinicalInstructor->assignRole('clinical_instructor');

        $examOfficer = User::firstOrCreate(
            ['email' => 'exam.officer@cnims.edu.ng'],
            ['name' => 'Mr. Chidi Eze (Exam Officer)', 'password' => Hash::make('password123')]
        );
        $examOfficer->assignRole('exam_officer');

        $financeOfficer = User::firstOrCreate(
            ['email' => 'finance.officer@cnims.edu.ng'],
            ['name' => 'Mr. Tunde Lawal (Finance Officer)', 'password' => Hash::make('password123')]
        );
        $financeOfficer->assignRole('finance_officer');

        $bursarUser = User::firstOrCreate(
            ['email' => 'bursar@cnims.edu.ng'],
            ['name' => 'Mrs. Kemi Ojo (Bursar)', 'password' => Hash::make('password123')]
        );
        $bursarUser->assignRole('bursar');

        // 5. Academic Structure
        $dept = Department::firstOrCreate(
            ['code' => 'NURS'],
            [
                'name' => 'Department of Nursing Sciences',
                'description' => 'Accredited Department providing Basic Nursing, Midwifery and Post-Basic Specialty qualifications.',
                'hod_id' => $hodUser->id,
            ]
        );

        $hodUser->update(['department_id' => $dept->id]);
        $lecturerUser->update(['department_id' => $dept->id]);

        $progBasic = Programme::firstOrCreate(
            ['code' => 'BN-RN'],
            [
                'department_id' => $dept->id,
                'name' => 'Basic Nursing Programme',
                'duration_years' => 3,
                'degree_type' => 'RN',
                'description' => '3-year general nursing training leading to Registered Nurse (RN) licensure.',
                'is_active' => true,
            ]
        );

        $progMidwifery = Programme::firstOrCreate(
            ['code' => 'PBM-RM'],
            [
                'department_id' => $dept->id,
                'name' => 'Post-Basic Midwifery Programme',
                'duration_years' => 2,
                'degree_type' => 'RM',
                'description' => 'Specialized maternal and neonatal care training leading to Registered Midwife (RM) licensure.',
                'is_active' => true,
            ]
        );

        $sessionCurrent = AcademicSession::firstOrCreate(
            ['name' => '2025/2026'],
            [
                'code' => '2025-2026',
                'start_date' => '2025-10-01',
                'end_date' => '2026-09-30',
                'status' => SessionStatus::ACTIVE,
                'is_current' => true,
            ]
        );

        $sem1 = AcademicSemester::firstOrCreate(
            ['academic_session_id' => $sessionCurrent->id, 'semester' => 1],
            ['name' => 'First Semester', 'code' => '2025-2026-S1', 'sequence' => 1, 'status' => 'active', 'start_date' => '2025-10-01', 'end_date' => '2026-03-31', 'is_current' => true]
        );

        $sem2 = AcademicSemester::firstOrCreate(
            ['academic_session_id' => $sessionCurrent->id, 'semester' => 2],
            ['name' => 'Second Semester', 'code' => '2025-2026-S2', 'sequence' => 2, 'status' => 'draft', 'start_date' => '2026-04-01', 'end_date' => '2026-09-30', 'is_current' => false]
        );

        $level100 = Level::firstOrCreate(['numeric_level' => 100], ['name' => 'Year 1', 'code' => '100L']);
        $level200 = Level::firstOrCreate(['numeric_level' => 200], ['name' => 'Year 2', 'code' => '200L']);
        $level300 = Level::firstOrCreate(['numeric_level' => 300], ['name' => 'Year 3', 'code' => '300L']);

        // 4. Courses
        $c101 = Course::firstOrCreate(
            ['code' => 'NUR 101'],
            [
                'department_id' => $dept->id,
                'programme_id' => $progBasic->id,
                'title' => 'Foundations of Professional Nursing',
                'credit_units' => 4,
                'level' => 100,
                'semester' => 1,
                'course_type' => 'core',
                'lecturer_id' => $lecturerUser->id,
                'description' => 'Historical development of nursing, ethical principles, asepsis, and fundamental patient comfort procedures.',
            ]
        );

        $c102 = Course::firstOrCreate(
            ['code' => 'NUR 102'],
            [
                'department_id' => $dept->id,
                'programme_id' => $progBasic->id,
                'title' => 'Human Anatomy & Physiology I',
                'credit_units' => 3,
                'level' => 100,
                'semester' => 1,
                'course_type' => 'core',
                'lecturer_id' => $hodUser->id,
                'description' => 'Detailed study of human skeletal, muscular, circulatory, and respiratory systems.',
            ]
        );

        $c103 = Course::firstOrCreate(
            ['code' => 'NUR 103'],
            [
                'department_id' => $dept->id,
                'programme_id' => $progBasic->id,
                'title' => 'Medical Microbiology & Parasitology',
                'credit_units' => 2,
                'level' => 100,
                'semester' => 1,
                'course_type' => 'required',
                'description' => 'Principles of infection prevention, pathogenesis, bacterial and viral identification.',
            ]
        );

        $c201 = Course::firstOrCreate(
            ['code' => 'NUR 201'],
            [
                'department_id' => $dept->id,
                'programme_id' => $progBasic->id,
                'title' => 'Medical-Surgical Nursing I',
                'credit_units' => 4,
                'level' => 200,
                'semester' => 1,
                'course_type' => 'core',
                'lecturer_id' => $lecturerUser->id,
                'description' => 'Comprehensive nursing care of adult patients undergoing surgical interventions and medical pathologies.',
            ]
        );
        $c201->prerequisites()->syncWithoutDetaching([$c101->id]);

        $c202 = Course::firstOrCreate(
            ['code' => 'NUR 202'],
            [
                'department_id' => $dept->id,
                'programme_id' => $progBasic->id,
                'title' => 'Pharmacology for Clinical Nursing',
                'credit_units' => 3,
                'level' => 200,
                'semester' => 1,
                'course_type' => 'core',
                'lecturer_id' => $hodUser->id,
                'description' => 'Drug actions, therapeutic indices, dosages calculation, adverse reactions, and safe IV administration.',
            ]
        );

        $c203 = Course::firstOrCreate(
            ['code' => 'NUR 203'],
            [
                'department_id' => $dept->id,
                'programme_id' => $progBasic->id,
                'title' => 'Health Assessment & Clinical Diagnostics',
                'credit_units' => 3,
                'level' => 200,
                'semester' => 1,
                'course_type' => 'core',
                'lecturer_id' => $lecturerUser->id,
                'description' => 'Techniques of physical examination, head-to-toe clinical assessment, and diagnostic interpretation.',
            ]
        );

        // Auto-provision Course Offerings for active session
        app(AcademicSessionTransitionService::class)->provisionCourseOfferings($sessionCurrent);

        // 5. Clinical Facilities & Wards
        $hospital1 = ClinicalFacility::firstOrCreate(
            ['name' => 'State University Teaching Hospital (SUTH)'],
            [
                'type' => 'teaching_hospital',
                'address' => 'Hospital Road, Sector 4, Capital City',
                'contact_person' => 'Matron Blessing Udoh',
                'phone' => '+234 803 111 2233',
                'email' => 'clinical@suth.org.ng',
                'is_active' => true,
            ]
        );

        $ward1 = ClinicalWard::firstOrCreate(
            ['clinical_facility_id' => $hospital1->id, 'name' => 'Male Medical Ward'],
            [
                'unit_code' => 'MMW-01',
                'bed_capacity' => 35,
                'student_capacity' => 12,
                'ward_in_charge' => 'Chief Nursing Officer A. Musa',
            ]
        );

        $ward2 = ClinicalWard::firstOrCreate(
            ['clinical_facility_id' => $hospital1->id, 'name' => 'Female Surgical Ward'],
            [
                'unit_code' => 'FSW-02',
                'bed_capacity' => 40,
                'student_capacity' => 15,
                'ward_in_charge' => 'Principal Nursing Officer R. Adeleke',
            ]
        );

        $ward3 = ClinicalWard::firstOrCreate(
            ['clinical_facility_id' => $hospital1->id, 'name' => 'Labour & Delivery Suite'],
            [
                'unit_code' => 'LDS-03',
                'bed_capacity' => 20,
                'student_capacity' => 8,
                'ward_in_charge' => 'Chief Midwife C. Nwosu',
            ]
        );

        // 6. Clinical Procedures Library
        $proc1 = ClinicalProcedure::firstOrCreate(
            ['code' => 'PRC-001'],
            ['title' => 'Wound Dressing & Surgical Asepsis', 'category' => 'Medical-Surgical', 'minimum_required_count' => 10]
        );
        $proc2 = ClinicalProcedure::firstOrCreate(
            ['code' => 'PRC-002'],
            ['title' => 'Intravenous Cannulation & Infusion Setup', 'category' => 'Clinical Skills', 'minimum_required_count' => 8]
        );
        $proc3 = ClinicalProcedure::firstOrCreate(
            ['code' => 'PRC-003'],
            ['title' => 'Urethral Catheterisation', 'category' => 'Medical-Surgical', 'minimum_required_count' => 5]
        );
        $proc4 = ClinicalProcedure::firstOrCreate(
            ['code' => 'PRC-004'],
            ['title' => 'Vital Signs Assessment & GCS Evaluation', 'category' => 'Patient Care', 'minimum_required_count' => 20]
        );

        // 7. Nursing Competency Engine Categories & Skills
        $catPatientCare = CompetencyCategory::firstOrCreate(
            ['name' => 'Patient Care & Physical Assessment'],
            ['description' => 'Essential nursing assessments, hygiene, vital signs monitoring, and patient safety.']
        );
        $skillVital = CompetencySkill::firstOrCreate(
            ['name' => 'Accurate Vital Signs & Neurological Check'],
            ['category_id' => $catPatientCare->id, 'target_level' => 'independent']
        );
        $skillAssess = CompetencySkill::firstOrCreate(
            ['name' => 'Comprehensive Head-to-Toe Assessment'],
            ['category_id' => $catPatientCare->id, 'target_level' => 'competent']
        );

        $catInvasive = CompetencyCategory::firstOrCreate(
            ['name' => 'Aseptic & Invasive Procedures'],
            ['description' => 'Sterile procedures, vascular access, and catheterisation.']
        );
        $skillCannula = CompetencySkill::firstOrCreate(
            ['name' => 'Peripheral IV Cannula Insertion'],
            ['category_id' => $catInvasive->id, 'target_level' => 'competent']
        );
        $skillWound = CompetencySkill::firstOrCreate(
            ['name' => 'Complex Surgical Wound Debridement & Dressing'],
            ['category_id' => $catInvasive->id, 'target_level' => 'competent']
        );

        // 8. Fee Structures (Bursar configurations)
        // 8a. Application Fee (NGN 15,000)
        $appFeeStruct = FeeStructure::firstOrCreate(
            [
                'fee_type' => 'application',
                'academic_session_id' => $sessionCurrent->id,
            ],
            [
                'programme_id' => null,
                'level' => 100,
                'title' => '2025/2026 Admissions Application Processing Fee',
                'total_amount' => 15000.00,
                'is_active' => true,
            ]
        );
        FeeItem::firstOrCreate(['fee_structure_id' => $appFeeStruct->id, 'name' => 'Application Form & CBT Screening Processing'], ['amount' => 15000.00]);

        // 8b. Acceptance Fee (NGN 35,000)
        $accFeeStruct = FeeStructure::firstOrCreate(
            [
                'fee_type' => 'acceptance',
                'academic_session_id' => $sessionCurrent->id,
            ],
            [
                'programme_id' => null,
                'level' => 100,
                'title' => '2025/2026 Provisional Admission Acceptance Fee',
                'total_amount' => 35000.00,
                'is_active' => true,
            ]
        );
        FeeItem::firstOrCreate(['fee_structure_id' => $accFeeStruct->id, 'name' => 'Non-Refundable Acceptance Levy & Dossier Setup'], ['amount' => 35000.00]);

        // 8c. Level 200 Tuition Fee
        $feeStruct = FeeStructure::firstOrCreate(
            [
                'programme_id' => $progBasic->id,
                'academic_session_id' => $sessionCurrent->id,
                'level' => 200,
                'fee_type' => 'tuition',
            ],
            [
                'title' => '2025/2026 Level 200 Basic Nursing Tuition & Fees',
                'total_amount' => 195000.00,
                'is_active' => true,
            ]
        );

        FeeItem::firstOrCreate(['fee_structure_id' => $feeStruct->id, 'name' => 'Tuition Fee'], ['amount' => 110000.00]);
        FeeItem::firstOrCreate(['fee_structure_id' => $feeStruct->id, 'name' => 'Clinical Posting & Hospital Levy'], ['amount' => 45000.00]);
        FeeItem::firstOrCreate(['fee_structure_id' => $feeStruct->id, 'name' => 'Semester Examination Fee'], ['amount' => 25000.00]);
        FeeItem::firstOrCreate(['fee_structure_id' => $feeStruct->id, 'name' => 'Nursing Library & ICT Fee'], ['amount' => 15000.00]);

        // 9. Sample Student User & Record
        $studentUser = User::firstOrCreate(
            ['email' => 'student@cnims.edu.ng'],
            ['name' => 'Jane Okon (Student Nurse)', 'password' => Hash::make('password123')]
        );
        $studentUser->assignRole('student');

        $student = Student::firstOrCreate(
            ['student_number' => 'CON/2026/00101'],
            [
                'user_id' => $studentUser->id,
                'programme_id' => $progBasic->id,
                'department_id' => $dept->id,
                'entry_session_id' => $sessionCurrent->id,
                'current_level_id' => $level200->id,
                'first_name' => 'Jane',
                'middle_name' => 'Chiamaka',
                'last_name' => 'Okon',
                'gender' => 'female',
                'date_of_birth' => '2004-05-14',
                'phone' => '+234 812 345 6789',
                'address' => 'Plot 12 Unity Close, Capital City',
                'state_of_origin' => 'Enugu',
                'lga' => 'Udi',
                'blood_group' => 'O+',
                'genotype' => 'AA',
                'emergency_contact_name' => 'Engr. Dennis Okon',
                'emergency_contact_phone' => '+234 802 987 6543',
                'emergency_contact_relationship' => 'Father',
                'status' => 'active',
                'admitted_at' => '2024-10-10',
            ]
        );

        // Student invoice & financial clearance
        $invoice = Invoice::firstOrCreate(
            ['invoice_number' => 'INV/2026/00101-A'],
            [
                'student_id' => $student->id,
                'fee_structure_id' => $feeStruct->id,
                'academic_session_id' => $sessionCurrent->id,
                'semester' => 1,
                'amount' => 195000.00,
                'paid_amount' => 195000.00,
                'balance' => 0.00,
                'status' => 'paid',
                'due_date' => now()->addDays(20),
            ]
        );

        FinancialClearance::firstOrCreate(
            [
                'student_id' => $student->id,
                'academic_session_id' => $sessionCurrent->id,
                'semester' => 1,
                'clearance_type' => 'course_registration',
            ],
            [
                'is_cleared' => true,
                'cleared_by' => $financeOfficer->id,
                'cleared_at' => now(),
                'remarks' => 'Full semester tuition verified. Cleared for course registration.',
            ]
        );

        // Student Course Registration
        $reg = StudentCourseRegistration::firstOrCreate(
            [
                'student_id' => $student->id,
                'academic_session_id' => $sessionCurrent->id,
                'semester' => 1,
            ],
            [
                'level' => 200,
                'total_credits' => 10,
                'status' => 'approved',
                'approved_by' => $hodUser->id,
                'approved_at' => now(),
                'remarks' => 'Approved by Head of Department',
            ]
        );

        StudentCourseRegistrationItem::firstOrCreate(['registration_id' => $reg->id, 'course_id' => $c201->id]);
        StudentCourseRegistrationItem::firstOrCreate(['registration_id' => $reg->id, 'course_id' => $c202->id]);
        StudentCourseRegistrationItem::firstOrCreate(['registration_id' => $reg->id, 'course_id' => $c203->id]);

        // Student Results for past courses (100L)
        StudentResult::firstOrCreate(
            ['student_id' => $student->id, 'course_id' => $c101->id],
            [
                'academic_session_id' => $sessionCurrent->id,
                'semester' => 1,
                'attempt_number' => 1,
                'attempt_type' => AttemptType::FIRST_ATTEMPT,
                'ca_score' => 27.5,
                'exam_score' => 52.0,
                'total_score' => 79.5,
                'grade' => 'A',
                'grade_point' => 5.0,
                'credit_unit' => $c101->credit_units,
                'quality_point' => 20.0,
                'credit_points' => 20.0,
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        StudentResult::firstOrCreate(
            ['student_id' => $student->id, 'course_id' => $c102->id],
            [
                'academic_session_id' => $sessionCurrent->id,
                'semester' => 1,
                'attempt_number' => 1,
                'attempt_type' => AttemptType::FIRST_ATTEMPT,
                'ca_score' => 25.0,
                'exam_score' => 45.0,
                'total_score' => 70.0,
                'grade' => 'A',
                'grade_point' => 5.0,
                'credit_unit' => $c102->credit_units,
                'quality_point' => 15.0,
                'credit_points' => 15.0,
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        StudentSemesterResult::firstOrCreate(
            ['student_id' => $student->id, 'academic_session_id' => $sessionCurrent->id, 'semester' => 1],
            [
                'level' => 100,
                'credits_registered' => 7,
                'credits_earned' => 7,
                'quality_points' => 35.0,
                'gpa' => 5.00,
                'cgpa' => 5.00,
                'academic_standing' => 'Good Standing',
                'is_published' => true,
            ]
        );

        // 10. Clinical Posting Assignment
        $posting = ClinicalPosting::firstOrCreate(
            ['title' => '2026 Batch A - Adult Medical Nursing Rotation'],
            [
                'programme_id' => $progBasic->id,
                'academic_session_id' => $sessionCurrent->id,
                'level' => 200,
                'facility_id' => $hospital1->id,
                'ward_id' => $ward1->id,
                'supervisor_id' => $clinicalInstructor->id,
                'start_date' => '2026-03-01',
                'end_date' => '2026-04-15',
                'max_capacity' => 15,
                'learning_objectives' => 'Master wound dressing under aseptic technique, patient medication administration, and vital signs charting.',
                'status' => 'active',
            ]
        );

        $posting->students()->syncWithoutDetaching([$student->id => [
            'attendance_rate' => 98.0,
            'performance_rating' => 'Very Good',
            'supervisor_comments' => 'Punctual, compassionate and clinically proficient.',
        ]]);

        // Student Logbook Entries
        ClinicalLogbook::firstOrCreate(
            ['student_id' => $student->id, 'procedure_id' => $proc1->id],
            [
                'clinical_posting_id' => $posting->id,
                'procedure_date' => '2026-03-12',
                'patient_reference_code' => 'PT-MED-042',
                'competency_level' => 'competent',
                'student_reflection' => 'Cleaned post-appendectomy abdominal wound with normal saline and applied povidone-iodine dressing without breaking sterile field.',
                'supervisor_id' => $clinicalInstructor->id,
                'status' => 'approved',
                'supervisor_remarks' => 'Good sterile boundary maintenance. Appropriate disposal of contaminated swabs.',
                'verified_at' => now(),
            ]
        );

        ClinicalLogbook::firstOrCreate(
            ['student_id' => $student->id, 'procedure_id' => $proc2->id],
            [
                'clinical_posting_id' => $posting->id,
                'procedure_date' => '2026-03-18',
                'patient_reference_code' => 'PT-MED-089',
                'competency_level' => 'supervised',
                'student_reflection' => 'Inserted 20G IV cannula into cephalic vein of adult patient under preceptor supervision.',
                'supervisor_id' => $clinicalInstructor->id,
                'status' => 'submitted',
            ]
        );

        // Competencies
        StudentCompetency::firstOrCreate(
            ['student_id' => $student->id, 'skill_id' => $skillVital->id],
            [
                'current_level' => 'independent',
                'assessed_by' => $clinicalInstructor->id,
                'assessed_at' => now(),
                'remarks' => 'Fully independent and consistently accurate in vital signs recording.',
            ]
        );

        StudentCompetency::firstOrCreate(
            ['student_id' => $student->id, 'skill_id' => $skillWound->id],
            [
                'current_level' => 'competent',
                'assessed_by' => $clinicalInstructor->id,
                'assessed_at' => now(),
                'remarks' => 'Demonstrated strict adherence to aseptic protocols.',
            ]
        );

        // 11. OSCE Examination
        $osce = OsceExamination::firstOrCreate(
            ['title' => 'Level 200 Mid-Session OSCE Clinical Examination'],
            [
                'programme_id' => $progBasic->id,
                'academic_session_id' => $sessionCurrent->id,
                'exam_date' => '2026-04-20',
                'total_stations' => 4,
                'total_possible_score' => 80.00,
                'status' => 'upcoming',
            ]
        );

        $station1 = OsceStation::firstOrCreate(
            ['osce_examination_id' => $osce->id, 'station_number' => 1],
            [
                'title' => 'Patient Identification & Medication Safety Check',
                'scenario' => 'Patient Mr. Audu requires 500mg IV Ceftriaxone. Verify patient ID, five rights of medication, and check allergies.',
                'allocated_time_minutes' => 7,
                'max_score' => 20.00,
                'assessor_id' => $clinicalInstructor->id,
            ]
        );

        OsceStationRubric::firstOrCreate(['osce_station_id' => $station1->id, 'criterion' => 'Proper hand hygiene and introductions', 'max_score' => 4.00]);
        OsceStationRubric::firstOrCreate(['osce_station_id' => $station1->id, 'criterion' => 'Confirms patient 2 unique identifiers (Name, DOB/Hosp No)', 'max_score' => 4.00]);
        OsceStationRubric::firstOrCreate(['osce_station_id' => $station1->id, 'criterion' => 'Verifies medication label against doctor chart and expiry', 'max_score' => 4.00]);
        OsceStationRubric::firstOrCreate(['osce_station_id' => $station1->id, 'criterion' => 'Checks known allergies and explains procedure to patient', 'max_score' => 4.00]);
        OsceStationRubric::firstOrCreate(['osce_station_id' => $station1->id, 'criterion' => 'Accurate documentation in MAR', 'max_score' => 4.00]);

        // 12. Sample Admissions Applications & Workflow Candidates
        // 12a. Blessing Danjuma - Primary Demo Persona (Step 7: Admission Offered, Acceptance Fee Pending)
        $applicantUser = User::firstOrCreate(
            ['email' => 'applicant@cnims.edu.ng'],
            ['name' => 'Blessing Danjuma', 'password' => Hash::make('password123')]
        );
        $applicantUser->assignRole('applicant');

        $appBlessing = Application::firstOrCreate(
            ['application_number' => 'APP/2026/00101'],
            [
                'user_id' => $applicantUser->id,
                'access_code' => 'CNIMS-SEC-7721',
                'programme_id' => $progBasic->id,
                'academic_session_id' => $sessionCurrent->id,
                'first_name' => 'Blessing',
                'middle_name' => 'Chiamaka',
                'last_name' => 'Danjuma',
                'email' => 'applicant@cnims.edu.ng',
                'phone' => '+234 814 789 0123',
                'gender' => 'female',
                'date_of_birth' => '2005-04-18',
                'state_of_origin' => 'Kaduna',
                'lga' => 'Chikun',
                'address' => '14 Independence Way, Kaduna',
                'secondary_school' => 'Queen Amina College, Kaduna',
                'secondary_school_sitting_1' => 'Queen Amina College, Kaduna',
                'secondary_school_sitting_2' => 'Government Secondary School, Zaria',
                'graduation_year' => 2024,
                'o_level_sittings' => 2,
                'o_level_sitting_1' => [
                    'exam_type' => 'WAEC',
                    'exam_year' => 2023,
                    'exam_number' => '4120938471',
                    'subjects' => [
                        'English Language' => 'B3',
                        'Mathematics' => 'C4',
                        'Biology' => 'B2',
                        'Chemistry' => 'D7',
                        'Physics' => 'C5',
                        'Civic Education' => 'B2',
                    ],
                ],
                'o_level_sitting_2' => [
                    'exam_type' => 'NECO',
                    'exam_year' => 2024,
                    'exam_number' => '5019283741',
                    'subjects' => [
                        'English Language' => 'C4',
                        'Mathematics' => 'C5',
                        'Biology' => 'C4',
                        'Chemistry' => 'B3',
                        'Physics' => 'C4',
                        'Agricultural Science' => 'A1',
                    ],
                ],
                'o_level_verified' => true,
                'o_level_credits_count' => 6,
                'jamb_reg_number' => '202610984721FA',
                'jamb_score' => 224,
                'jamb_subjects' => [
                    'Use of English' => 62,
                    'Biology' => 58,
                    'Chemistry' => 54,
                    'Physics' => 50,
                ],
                'application_fee_paid' => true,
                'application_fee_paid_at' => now()->subDays(10),
                'entrance_exam_invited' => true,
                'entrance_exam_date' => now()->subDays(6)->setTime(9, 0),
                'entrance_exam_venue' => 'CNIMS CBT Center, ICT Complex, Hall A',
                'entrance_exam_seat_number' => 'CBT-A-042',
                'entrance_exam_invited_at' => now()->subDays(12),
                'entrance_exam_score' => 78.50,
                'entrance_exam_remarks' => 'Demonstrated strong analytical and scientific aptitude. Highly recommended for General Nursing.',
                'entrance_exam_scored_at' => now()->subDays(5),
                'entrance_exam_scored_by' => $adminUser->id,
                'admission_offered_at' => now()->subDays(3),
                'acceptance_deadline' => now()->addDays(14),
                'admission_letter_ref' => 'CNIMS/ADM/2026/00101',
                'acceptance_fee_paid' => false,
                'acceptance_fee_paid_at' => null,
                'status' => 'admitted',
                'submitted_at' => now()->subDays(14),
            ]
        );

        // Application Fee Invoice & Payment for Blessing
        $blessingAppInvoice = Invoice::firstOrCreate(
            ['invoice_number' => 'INV/APP/2026/00101'],
            [
                'fee_structure_id' => $appFeeStruct->id,
                'application_id' => $appBlessing->id,
                'student_id' => null,
                'academic_session_id' => $sessionCurrent->id,
                'invoice_type' => 'application_fee',
                'amount' => 15000.00,
                'paid_amount' => 15000.00,
                'balance' => 0.00,
                'status' => 'paid',
                'due_date' => now()->subDays(10),
            ]
        );

        Payment::firstOrCreate(
            ['transaction_reference' => 'PAY-APP-202600101-01'],
            [
                'invoice_id' => $blessingAppInvoice->id,
                'application_id' => $appBlessing->id,
                'student_id' => null,
                'amount' => 15000.00,
                'payment_method' => 'bank_transfer',
                'receipt_number' => 'REC-APP-2026-00101',
                'status' => 'successful',
                'paid_at' => now()->subDays(10),
            ]
        );

        // Acceptance Fee Invoice (unpaid) for Blessing
        Invoice::firstOrCreate(
            ['invoice_number' => 'INV/ACC/2026/00101'],
            [
                'fee_structure_id' => $accFeeStruct->id,
                'application_id' => $appBlessing->id,
                'student_id' => null,
                'academic_session_id' => $sessionCurrent->id,
                'invoice_type' => 'acceptance_fee',
                'amount' => 35000.00,
                'paid_amount' => 0.00,
                'balance' => 35000.00,
                'status' => 'unpaid',
                'due_date' => now()->addDays(14),
            ]
        );

        // 12b. Amina Usman (Step 5 candidate: Application Fee Paid, Ready for CBT Exam Invitation)
        $appAmina = Application::firstOrCreate(
            ['application_number' => 'APP/2026/0001-NURS'],
            [
                'programme_id' => $progBasic->id,
                'academic_session_id' => $sessionCurrent->id,
                'access_code' => 'CNIMS-SEC-1102',
                'first_name' => 'Amina',
                'middle_name' => 'Zahra',
                'last_name' => 'Usman',
                'email' => 'amina.usman@example.com',
                'phone' => '+234 809 112 3344',
                'gender' => 'female',
                'date_of_birth' => '2005-08-22',
                'state_of_origin' => 'Kaduna',
                'lga' => 'Zaria',
                'address' => '24 Crescent Road, Zaria',
                'secondary_school' => 'Federal Government Girls College, Zaria',
                'graduation_year' => 2023,
                'o_level_sittings' => 1,
                'o_level_sitting_1' => [
                    'exam_type' => 'WAEC',
                    'exam_year' => 2023,
                    'exam_number' => '3194820194',
                    'subjects' => [
                        'English Language' => 'B2',
                        'Mathematics' => 'B3',
                        'Biology' => 'A1',
                        'Chemistry' => 'B2',
                        'Physics' => 'B3',
                    ],
                ],
                'o_level_verified' => true,
                'o_level_credits_count' => 5,
                'jamb_reg_number' => '202619482012AB',
                'jamb_score' => 238,
                'jamb_subjects' => [
                    'Use of English' => 65,
                    'Biology' => 64,
                    'Chemistry' => 58,
                    'Physics' => 51,
                ],
                'application_fee_paid' => true,
                'application_fee_paid_at' => now()->subDays(5),
                'status' => 'submitted',
                'submitted_at' => now()->subDays(5),
            ]
        );

        $aminaInvoice = Invoice::firstOrCreate(
            ['invoice_number' => 'INV/APP/2026/00001'],
            [
                'fee_structure_id' => $appFeeStruct->id,
                'application_id' => $appAmina->id,
                'student_id' => null,
                'academic_session_id' => $sessionCurrent->id,
                'invoice_type' => 'application_fee',
                'amount' => 15000.00,
                'paid_amount' => 15000.00,
                'balance' => 0.00,
                'status' => 'paid',
                'due_date' => now()->subDays(5),
            ]
        );

        Payment::firstOrCreate(
            ['transaction_reference' => 'PAY-APP-202600001-01'],
            [
                'invoice_id' => $aminaInvoice->id,
                'application_id' => $appAmina->id,
                'student_id' => null,
                'amount' => 15000.00,
                'payment_method' => 'card',
                'receipt_number' => 'REC-APP-2026-00001',
                'status' => 'successful',
                'paid_at' => now()->subDays(5),
            ]
        );

        // 12c. Blessing Kalu (Step 6 candidate: CBT Invited, Ready for Entrance Scoring)
        $appKalu = Application::firstOrCreate(
            ['application_number' => 'APP/2026/0002-MIDW'],
            [
                'programme_id' => $progMidwifery->id,
                'academic_session_id' => $sessionCurrent->id,
                'access_code' => 'CNIMS-SEC-3391',
                'first_name' => 'Blessing',
                'middle_name' => 'Chinyere',
                'last_name' => 'Kalu',
                'email' => 'blessing.kalu@example.com',
                'phone' => '+234 813 998 7766',
                'gender' => 'female',
                'date_of_birth' => '2003-11-05',
                'state_of_origin' => 'Abia',
                'lga' => 'Ohafia',
                'address' => '5 Market Avenue, Aba',
                'secondary_school' => 'Queen of Apostles Secondary School',
                'graduation_year' => 2021,
                'previous_qualification' => 'Registered General Nurse (RN) License #784912',
                'o_level_sittings' => 1,
                'o_level_sitting_1' => [
                    'exam_type' => 'WAEC',
                    'exam_year' => 2021,
                    'exam_number' => '1209384711',
                    'subjects' => [
                        'English Language' => 'A1',
                        'Mathematics' => 'C4',
                        'Biology' => 'B2',
                        'Chemistry' => 'B3',
                        'Physics' => 'C5',
                    ],
                ],
                'o_level_verified' => true,
                'o_level_credits_count' => 5,
                'jamb_reg_number' => '202639102948BC',
                'jamb_score' => 210,
                'jamb_subjects' => [
                    'Use of English' => 58,
                    'Biology' => 55,
                    'Chemistry' => 50,
                    'Physics' => 47,
                ],
                'application_fee_paid' => true,
                'application_fee_paid_at' => now()->subDays(4),
                'entrance_exam_invited' => true,
                'entrance_exam_date' => now()->addDays(3)->setTime(10, 0),
                'entrance_exam_venue' => 'CNIMS CBT Center, ICT Complex, Hall B',
                'entrance_exam_seat_number' => 'CBT-B-018',
                'entrance_exam_invited_at' => now()->subDays(2),
                'status' => 'shortlisted',
                'submitted_at' => now()->subDays(4),
            ]
        );

        $kaluInvoice = Invoice::firstOrCreate(
            ['invoice_number' => 'INV/APP/2026/00002'],
            [
                'fee_structure_id' => $appFeeStruct->id,
                'application_id' => $appKalu->id,
                'student_id' => null,
                'academic_session_id' => $sessionCurrent->id,
                'invoice_type' => 'application_fee',
                'amount' => 15000.00,
                'paid_amount' => 15000.00,
                'balance' => 0.00,
                'status' => 'paid',
                'due_date' => now()->subDays(4),
            ]
        );

        Payment::firstOrCreate(
            ['transaction_reference' => 'PAY-APP-202600002-01'],
            [
                'invoice_id' => $kaluInvoice->id,
                'application_id' => $appKalu->id,
                'student_id' => null,
                'amount' => 15000.00,
                'payment_method' => 'card',
                'receipt_number' => 'REC-APP-2026-00002',
                'status' => 'successful',
                'paid_at' => now()->subDays(4),
            ]
        );
    }
}
