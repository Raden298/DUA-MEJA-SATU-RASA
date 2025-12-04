<?php
session_start();
session_destroy();
echo "Session dibersihkan. <a href='login.php'>Kembali ke login</a>";
