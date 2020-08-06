<?php


use Models\Employee;



$employees = Employee::all();
   
foreach ($employees as $employee)
    echo $employee->name;