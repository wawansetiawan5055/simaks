<?php
echo shell_exec('ln -snf ../cbt/public cbt 2>&1');
echo shell_exec('ls -la cbt 2>&1');
?>