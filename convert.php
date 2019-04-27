<?php

require 'scripts/functions.php';
loadClasses();

$configuration = new Configuration(
    findThesis(),
    baseDir() . '/outputs/',
    'Diplomová práce',
    'Petr Hladík'
);


$configuration->addCitation('c0bf19caab884610945703b42983ab27', '@online{c0bf19caab884610945703b42983ab27,
    title     = "Open Source Search & Analytics · Elasticsearch | Elastic",
    url       = "https://www.elastic.co/brand",
}
');
$configuration->addCitation('6347ebcf49f74f4b627bb615c2dbd0e6', '@book{6347ebcf49f74f4b627bb615c2dbd0e6:,
    title = {PHP and MySQL : 24-hour trainer},
    language = {eng},
    address = {Indianapolis},
    author = {Tarr, Andrea},
    keywords = {programování vt; data ob; programovací jazyky vt; databázové systémy vt; MySQL; PHP; databáze},
    publisher = {Wiley},
    isbn = {978-1-118-06688-1},
    year = {2012},
}
');
$configuration->addCitation('6347ebcf49f74f4b627bb615c2dbd0e6', '@misc{6347ebcf49f74f4b627bb615c2dbd0e6,
    title = {Návrh informačního systému},
    language = {cze},
    author = {Horák, Martin},
    keywords = {Návrh informačního systému; Informační systém; PHP; MySQL; DF diagramy; ER diagramy; databáze; Design of an Information systém; Information systém; PHP; MySQL; DF diagrams; ER diagrams; database},
    abstract = {Cílem této diplomové práce je návrh informačního systému, zaměřujícího se na správu koncertů a vystoupení hudebního tělesa. Informační systém je navržen ve spolupráci se společností Limeta Apps s.r.o},
    publisher = {Vysoké učení technické v Brně. Fakulta podnikatelská},
    year = {2015},
}
');
$configuration->addCitation('7622817655636d69b1021e970cc942c5', '@misc{7622817655636d69b1021e970cc942c5,
    title = {1001 tipů a triků pro PHP},
    edition = {Vyd. 1.},
    language = {cze},
    address = {Brno},
    author = {Vrána, Jakub},
    keywords = {objektově orientované programování vt; programovací jazyk PHP vt; PHP; XML dokumenty; skriptové jazyky; webové aplikace},
    publisher = {Computer Press},
    isbn = {978-80-251-2940-1},
    year = {2010},
}
');


(new Convertor($configuration))->convert();