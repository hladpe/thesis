<?php

require 'functions.php';

loadClasses();

(new Convertor())->convert(findThesis(), baseDir() . '/outputs/thesis.tex');