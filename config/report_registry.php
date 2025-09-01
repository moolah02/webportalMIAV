<?php
return [
  'bases' => ['clients','pos_terminals','visits','job_assignments','tickets'],

  'joins' => [
    // terminals → clients
    ['from'=>['table'=>'pos_terminals','column'=>'client_id'],      'to'=>['table'=>'clients','column'=>'id']],
    // tickets → terminals / clients
    ['from'=>['table'=>'tickets','column'=>'pos_terminal_id'],      'to'=>['table'=>'pos_terminals','column'=>'id']],
    ['from'=>['table'=>'tickets','column'=>'client_id'],            'to'=>['table'=>'clients','column'=>'id']],
    // job_assignments → clients / regions / employees
    ['from'=>['table'=>'job_assignments','column'=>'client_id'],    'to'=>['table'=>'clients','column'=>'id']],
    ['from'=>['table'=>'job_assignments','column'=>'region_id'],    'to'=>['table'=>'regions','column'=>'id']],
    ['from'=>['table'=>'job_assignments','column'=>'technician_id'],'to'=>['table'=>'employees','column'=>'id']],
    // visits → visit_terminals → pos_terminals → clients
    ['from'=>['table'=>'visit_terminals','column'=>'visit_id'],     'to'=>['table'=>'visits','column'=>'id']],
    ['from'=>['table'=>'visit_terminals','column'=>'pos_terminal_id'],'to'=>['table'=>'pos_terminals','column'=>'id']],
    ['from'=>['table'=>'pos_terminals','column'=>'client_id'],      'to'=>['table'=>'clients','column'=>'id']],
    // visits → employees (technician who did the visit)
    ['from'=>['table'=>'visits','column'=>'employee_id'],           'to'=>['table'=>'employees','column'=>'id']],
  ],

  'fields' => [
    'clients' => [
      ['key'=>'clients.id','label'=>'Client ID','type'=>'number'],
      ['key'=>'clients.company_name','label'=>'Client Name','type'=>'string'],
      ['key'=>'clients.region','label'=>'Client Region (text)','type'=>'string'],
      ['key'=>'clients.status','label'=>'Client Status','type'=>'string'],
      ['key'=>'clients.contract_start_date','label'=>'Contract Start','type'=>'date'],
      ['key'=>'clients.contract_end_date','label'=>'Contract End','type'=>'date'],
    ],
    'pos_terminals' => [
      ['key'=>'pos_terminals.id','label'=>'Terminal PK','type'=>'number'],
      ['key'=>'pos_terminals.terminal_id','label'=>'Terminal ID (string)','type'=>'string'],
      ['key'=>'pos_terminals.client_id','label'=>'Client ID','type'=>'number'],
      ['key'=>'pos_terminals.current_status','label'=>'Terminal Status (current)','type'=>'string'],
      ['key'=>'pos_terminals.region_id','label'=>'Region ID','type'=>'number'],
      ['key'=>'pos_terminals.city','label'=>'City','type'=>'string'],
      ['key'=>'pos_terminals.business_type','label'=>'Business Type','type'=>'string'],
      ['key'=>'pos_terminals.installation_date','label'=>'Installation Date','type'=>'date'],
    ],
    'visits' => [
      ['key'=>'visits.id','label'=>'Visit ID','type'=>'number'],
      ['key'=>'visits.merchant_id','label'=>'Merchant ID (holder)','type'=>'number'],
      ['key'=>'visits.merchant_name','label'=>'Merchant Name','type'=>'string'],
      ['key'=>'visits.employee_id','label'=>'Technician ID','type'=>'number'],
      ['key'=>'visits.completed_at','label'=>'Visit Completed At','type'=>'datetime'],
      ['key'=>'visits.visit_summary','label'=>'Visit Summary','type'=>'string'],
    ],
    'job_assignments' => [
      ['key'=>'job_assignments.id','label'=>'Assignment PK','type'=>'number'],
      ['key'=>'job_assignments.assignment_id','label'=>'Assignment Code','type'=>'string'],
      ['key'=>'job_assignments.client_id','label'=>'Client ID','type'=>'number'],
      ['key'=>'job_assignments.technician_id','label'=>'Technician ID','type'=>'number'],
      ['key'=>'job_assignments.region_id','label'=>'Region ID','type'=>'number'],
      ['key'=>'job_assignments.status','label'=>'Assignment Status','type'=>'string'],
      ['key'=>'job_assignments.priority','label'=>'Assignment Priority','type'=>'string'],
      ['key'=>'job_assignments.scheduled_date','label'=>'Scheduled Date','type'=>'date'],
      ['key'=>'job_assignments.actual_start_time','label'=>'Actual Start','type'=>'datetime'],
      ['key'=>'job_assignments.actual_end_time','label'=>'Actual End','type'=>'datetime'],
    ],
    'tickets' => [
      ['key'=>'tickets.id','label'=>'Ticket PK','type'=>'number'],
      ['key'=>'tickets.ticket_id','label'=>'Ticket Code','type'=>'string'],
      ['key'=>'tickets.client_id','label'=>'Client ID','type'=>'number'],
      ['key'=>'tickets.pos_terminal_id','label'=>'Terminal PK','type'=>'number'],
      ['key'=>'tickets.priority','label'=>'Priority','type'=>'string'],
      ['key'=>'tickets.status','label'=>'Status','type'=>'string'],
      ['key'=>'tickets.title','label'=>'Title','type'=>'string'],
      ['key'=>'tickets.created_at','label'=>'Created At','type'=>'datetime'],
      ['key'=>'tickets.resolved_at','label'=>'Resolved At','type'=>'datetime'],
    ],
    // employees (for display/grouping)
    'employees' => [
      ['key'=>'employees.id','label'=>'Employee ID','type'=>'number'],
      ['key'=>'employees.first_name','label'=>'First Name','type'=>'string'],
      ['key'=>'employees.last_name','label'=>'Last Name','type'=>'string'],
      ['key'=>'employees.status','label'=>'Employee Status','type'=>'string'],
      ['key'=>'employees.department_id','label'=>'Department ID','type'=>'number'],
      ['key'=>'employees.role_id','label'=>'Role ID','type'=>'number'],
    ],

  ],
];
