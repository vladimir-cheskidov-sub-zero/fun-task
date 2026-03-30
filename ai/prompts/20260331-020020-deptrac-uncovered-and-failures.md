- deptrac-uncovered-and-failures
Обнови @deptrac.yaml 
Сейчас он выдает:
 -------------------- ----- 
  Report                    
 -------------------- ----- 
  Violations           0    
  Skipped violations   0    
  Uncovered            56   
  Allowed              92   
  Warnings             0    
  Errors               0    
 -------------------- ----- 
 Uncovered должно быть равно нулю.
 Обнови composer.json, чтобы запуск deptrac завершался ошибкой если что либо кроме Allowed больше нуля
