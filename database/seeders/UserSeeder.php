<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
          [
            'id' => '9ff18c61-8055-40d0-b119-6043d9ee1fc2',
            'external_id' => '019956a6-2c8b-72fb-bc11-933a7fe3a7ee',
            'name' => 'Ajeng Suartini S.IP',
            'email' => 'admin@jti.com',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiMDE5OTU2YTYtMmM4Yi03MmZiLWJjMTEtOTMzYTdmZTNhN2VlIiwicm9sZXMiOlsiYWRtaW4iXSwicGVybWlzc2lvbnMiOlsiY3JlYXRlX21fY2xhc3MiLCJ1cGRhdGVfbV9vYXV0aF9jbGllbnQiLCJkZWxldGVfbV9zdWJqZWN0X2xlY3R1cmUiLCJjcmVhdGVfbV9zdWJqZWN0X3NlbWVzdGVyIiwiZGVsZXRlX21fZW1wbG95ZWVfbGFiIiwicmVhZF9tX3Nlc3Npb24iLCJjcmVhdGVfbV9zdWJqZWN0IiwiZGVsZXRlX21fc3ViamVjdF9zZW1lc3RlciIsImNyZWF0ZV9tX21ham9yIiwidXBkYXRlX21fc3ViamVjdF9sZWN0dXJlIiwiY3JlYXRlX21fZW1wbG95ZWVfbGFiIiwidXBkYXRlX21fc2VtZXN0ZXIiLCJkZWxldGVfbV91c2VyIiwicmVhZF9tX3N1YmplY3Rfc2VtZXN0ZXIiLCJyZWFkX21fZW1wbG95ZWVfbGFiIiwiZGVsZXRlX21fbWFqb3IiLCJyZWFkX21fb2F1dGhfY2xpZW50IiwidXBkYXRlX21fc3ViamVjdCIsInJlYWRfbV91c2VyIiwicmVhZF9tX2xhYiIsInJlYWRfbV9lbXBsb3llZSIsImRlbGV0ZV9tX2VtcGxveWVlIiwiY3JlYXRlX21fbGFiIiwicmVhZF9tX3N0dWR5X3Byb2dyYW0iLCJ1cGRhdGVfbV9lbXBsb3llZSIsInVwZGF0ZV9tX2VtcGxveWVlX2xhYiIsInJlYWRfbV9tYWpvciIsImNyZWF0ZV9tX3VzZXIiLCJ1cGRhdGVfbV9jbGFzcyIsImRlbGV0ZV9tX3N0dWRlbnQiLCJ1cGRhdGVfbV9sYWIiLCJjcmVhdGVfbV9zZXNzaW9uIiwidXBkYXRlX21fc3R1ZGVudCIsInVwZGF0ZV9tX3N0dWRlbnRfc2VtZXN0ZXIiLCJ1cGRhdGVfbV91c2VyIiwiY3JlYXRlX21fc2VtZXN0ZXIiLCJjcmVhdGVfbV9lbXBsb3llZSIsImNyZWF0ZV9tX29hdXRoX2NsaWVudCIsImNyZWF0ZV9tX3N0dWRlbnQiLCJjcmVhdGVfbV9zdHVkZW50X3NlbWVzdGVyIiwiZGVsZXRlX21fc3R1ZGVudF9zZW1lc3RlciIsImRlbGV0ZV9tX3N1YmplY3QiLCJyZWFkX21fc2VtZXN0ZXIiLCJjcmVhdGVfbV9zdHVkeV9wcm9ncmFtIiwiZGVsZXRlX21fbGFiIiwiY3JlYXRlX21fc3ViamVjdF9sZWN0dXJlIiwiZGVsZXRlX21fY2xhc3MiLCJkZWxldGVfbV9vYXV0aF9jbGllbnQiLCJkZWxldGVfbV9zZXNzaW9uIiwicmVhZF9tX3N0dWRlbnQiLCJyZWFkX21fc3R1ZGVudF9zZW1lc3RlciIsInVwZGF0ZV9tX3N0dWR5X3Byb2dyYW0iLCJ1cGRhdGVfbV9tYWpvciIsInJlYWRfbV9zdWJqZWN0IiwicmVhZF9tX3N1YmplY3RfbGVjdHVyZSIsInVwZGF0ZV9tX3N1YmplY3Rfc2VtZXN0ZXIiLCJyZWFkX21fY2xhc3MiLCJ1cGRhdGVfbV9zZXNzaW9uIiwiZGVsZXRlX21fc3R1ZHlfcHJvZ3JhbSIsImRlbGV0ZV9tX3NlbWVzdGVyIl0sImV4cCI6MTc1ODY4NDMyMCwiaWF0IjoxNzU4NTk3OTIwfQ.mNjF1EJ6Yj8M-rw-HeAjmxeW1bL2DEL4M9zdnBGnnQ0',
            'roles' => '["admin"]',
            'permissions' => '["create_m_class","update_m_oauth_client","delete_m_subject_lecture","create_m_subject_semester","delete_m_employee_lab","read_m_session","create_m_subject","delete_m_subject_semester","create_m_major","update_m_subject_lecture","create_m_employee_lab","update_m_semester","delete_m_user","read_m_subject_semester","read_m_employee_lab","delete_m_major","read_m_oauth_client","update_m_subject","read_m_user","read_m_lab","read_m_employee","delete_m_employee","create_m_lab","read_m_study_program","update_m_employee","update_m_employee_lab","read_m_major","create_m_user","update_m_class","delete_m_student","update_m_lab","create_m_session","update_m_student","update_m_student_semester","update_m_user","create_m_semester","create_m_employee","create_m_oauth_client","create_m_student","create_m_student_semester","delete_m_student_semester","delete_m_subject","read_m_semester","create_m_study_program","delete_m_lab","create_m_subject_lecture","delete_m_class","delete_m_oauth_client","delete_m_session","read_m_student","read_m_student_semester","update_m_study_program","update_m_major","read_m_subject","read_m_subject_lecture","update_m_subject_semester","read_m_class","update_m_session","delete_m_study_program","delete_m_semester"]',
          ],
          [
            'id' => '9ff18c6b-2b68-4d0c-b521-d1163ed0a10f',
            'external_id' => '3bb1a5c6-8e7d-4b74-881b-6442d83f28f2',
            'name' => 'Abdullah Muchsin',
            'email' => 'e41230028@student.polije.ac.id',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiM2JiMWE1YzYtOGU3ZC00Yjc0LTg4MWItNjQ0MmQ4M2YyOGYyIiwicm9sZXMiOlsic3R1ZGVudCJdLCJwZXJtaXNzaW9ucyI6bnVsbCwiZXhwIjoxNzU4Njg0MzI2LCJpYXQiOjE3NTg1OTc5MjZ9.SeiK7XANJbYUBCC1ub4pvtu_-ZpYTeGJvX8NE0E6fls',
            'roles' => '["student"]',
            'permissions' => '',
          ],
          [
            'id' => '9ff18cbb-2b48-4042-aa25-61b8702cd934',
            'external_id' => '280f46d5-c81b-4929-89b2-1b7ffff68916',
            'name' => 'A. Ilham Bintang',
            'email' => 'e41231080@student.polije.ac.id',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiMjgwZjQ2ZDUtYzgxYi00OTI5LTg5YjItMWI3ZmZmZjY4OTE2Iiwicm9sZXMiOlsic3R1ZGVudCJdLCJwZXJtaXNzaW9ucyI6bnVsbCwiZXhwIjoxNzU4Njg0Mzc5LCJpYXQiOjE3NTg1OTc5Nzl9.T54-ugWPD9KnHkswGeXpAr08g87J10q9HFVyP-zvcuw',
            'roles' => '["student"]',
            'permissions' => '',
          ],
          [
            'id' => '9ff19449-518b-477e-815a-aa49284f1822',
            'external_id' => '856e1114-eac7-44b8-9983-bc3b3986fdfb',
            'name' => 'Aan Nur',
            'email' => 'e41182273@polije.ac.id',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiODU2ZTExMTQtZWFjNy00NGI4LTk5ODMtYmMzYjM5ODZmZGZiIiwicm9sZXMiOlsic3R1ZGVudCJdLCJwZXJtaXNzaW9ucyI6bnVsbCwiZXhwIjoxNzU4Njg1NjM2LCJpYXQiOjE3NTg1OTkyMzZ9.PVujDPoeFbwT7LcpE0LChB-aKWR_vbkwK31poJ9HuBw',
            'roles' => '["student"]',
            'permissions' => '',
          ],
          [
            'id' => '9ff19466-2ba5-4002-bb7c-ba3882a92dc2',
            'external_id' => '309f7b76-8f73-42f5-8d30-ea3c9a621899',
            'name' => 'Abadan Wahyu',
            'email' => 'e41192044@polije.ac.id',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiMzA5ZjdiNzYtOGY3My00MmY1LThkMzAtZWEzYzlhNjIxODk5Iiwicm9sZXMiOlsic3R1ZGVudCJdLCJwZXJtaXNzaW9ucyI6bnVsbCwiZXhwIjoxNzU4Njg1NjU1LCJpYXQiOjE3NTg1OTkyNTV9.9Pt_HZ2xFuTYsSEmuXs-niUgtxox1YmAj6jt9WjTdDc',
            'roles' => '["student"]',
            'permissions' => '',
          ],
          [
            'id' => '9ff19487-4cf1-4ca0-a700-47beb0b53c2a',
            'external_id' => 'c25b1069-124f-4791-b7ec-bfc258d1be75',
            'name' => 'Abd. Halim',
            'email' => 'e32200179@polije.ac.id',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiYzI1YjEwNjktMTI0Zi00NzkxLWI3ZWMtYmZjMjU4ZDFiZTc1Iiwicm9sZXMiOlsic3R1ZGVudCJdLCJwZXJtaXNzaW9ucyI6bnVsbCwiZXhwIjoxNzU4Njg1Njc3LCJpYXQiOjE3NTg1OTkyNzd9.4zW4LPR5g7HySuxI54FUWj-zRoNznr-Rsam-a7nEUis',
            'roles' => '["student"]',
            'permissions' => '',
          ]
        ];

        foreach ($users as $user) {
            User::factory()->create($user);
        }
    }
}
