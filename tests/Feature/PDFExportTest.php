<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DTR;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PDFExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);
        
        // Create test employee
        $this->employee = User::factory()->create([
            'role' => 'employee',
            'employee_id' => 'EMP001',
            'department' => 'IT',
            'position' => 'Developer'
        ]);
        
        // Create test DTR records
        $this->createTestDTRRecords();
    }

    private function createTestDTRRecords()
    {
        $today = Carbon::today();
        
        for ($i = 0; $i < 5; $i++) {
            $date = $today->copy()->subDays($i);
            
            DTR::create([
                'user_id' => $this->employee->id,
                'date' => $date,
                'time_in' => $date->copy()->setTime(8, 0, 0),
                'time_out' => $date->copy()->setTime(17, 0, 0),
                'break_start' => $date->copy()->setTime(12, 0, 0),
                'break_end' => $date->copy()->setTime(13, 0, 0),
                'break_hours' => 1.0,
                'total_hours' => 8.0,
                'status' => 'present',
            ]);
        }
    }

    public function test_pdf_export_requires_authentication()
    {
        $response = $this->get('/dtr/export-pdf');
        $response->assertRedirect('/admin/login');
    }

    public function test_pdf_export_validates_required_fields()
    {
        $this->actingAs($this->admin);
        
        $response = $this->get('/dtr/export-pdf');
        $response->assertSessionHasErrors(['user_id', 'from_date', 'to_date']);
    }

    public function test_pdf_export_generates_pdf_successfully()
    {
        $this->actingAs($this->admin);
        
        $fromDate = Carbon::today()->subDays(4)->format('Y-m-d');
        $toDate = Carbon::today()->format('Y-m-d');
        
        $response = $this->get("/dtr/export-pdf?user_id={$this->employee->id}&from_date={$fromDate}&to_date={$toDate}");
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        
        // Check if the response contains PDF content
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pdf_export_with_invalid_user()
    {
        $this->actingAs($this->admin);
        
        $fromDate = Carbon::today()->subDays(4)->format('Y-m-d');
        $toDate = Carbon::today()->format('Y-m-d');
        
        $response = $this->get("/dtr/export-pdf?user_id=999&from_date={$fromDate}&to_date={$toDate}");
        
        $response->assertSessionHasErrors(['user_id']);
    }

    public function test_pdf_export_with_invalid_date_range()
    {
        $this->actingAs($this->admin);
        
        // Future date should fail
        $fromDate = Carbon::tomorrow()->format('Y-m-d');
        $toDate = Carbon::tomorrow()->addDays(1)->format('Y-m-d');
        
        $response = $this->get("/dtr/export-pdf?user_id={$this->employee->id}&from_date={$fromDate}&to_date={$toDate}");
        
        $response->assertSessionHasErrors(['from_date']);
    }

    public function test_pdf_export_filename_format()
    {
        $this->actingAs($this->admin);
        
        $fromDate = Carbon::today()->subDays(4)->format('Y-m-d');
        $toDate = Carbon::today()->format('Y-m-d');
        
        $response = $this->get("/dtr/export-pdf?user_id={$this->employee->id}&from_date={$fromDate}&to_date={$toDate}");
        
        $response->assertStatus(200);
        
        // Check if the filename is in the expected format
        $expectedFilename = 'DTR_Report_' . $this->employee->employee_id . '_' . $fromDate . '_to_' . $toDate . '.pdf';
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . $expectedFilename . '"');
    }
}
