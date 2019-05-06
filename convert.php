<?php

require 'scripts/functions.php';
loadClasses();

$configuration = new Configuration(
    findThesis(),
    baseDir() . '/outputs/',
    'Diplomová práce',
    'Petr Hladík'
);


$configuration->addCitation('c0bf19caab884610945703b42983ab27', '@misc{c0bf19caab884610945703b42983ab27,
    title     = "Open Source Search \& Analytics · Elasticsearch | Elastic",
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
$configuration->addCitation('3ecb1b67a38b2feb5847c8682a22de14', '@misc{3ecb1b67a38b2feb5847c8682a22de14,
    title = {Návrh informačního systému},
    language = {cze},
    author = {Horák, Martin},
    keywords = {Návrh informačního systému; Informační systém; PHP; MySQL; DF diagramy; ER diagramy; databáze; Design of an Information systém; Information systém; PHP; MySQL; DF diagrams; ER diagrams; database},
    abstract = {Cílem této diplomové práce je návrh informačního systému, zaměřujícího se na správu koncertů a vystoupení hudebního tělesa. Informační systém je navržen ve spolupráci se společností Limeta Apps s.r.o},
    publisher = {Vysoké učení technické v Brně. Fakulta podnikatelská},
    year = {2015},
}
');
$configuration->addCitation('7622817655636d69b1021e970cc942c5', '@book{7622817655636d69b1021e970cc942c5,
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
$configuration->addCitation('ad3fe4aaf1f8548d92c29d62dcf23cdb', '@book{ad3fe4aaf1f8548d92c29d62dcf23cdb,
    title = {PHP 5 a MySQL 5 : průvodce webového programátora},
    edition = {Vyd. 1.},
    language = {cze},
    address = {Brno},
    author = {Kofler, Michael},
    keywords = {internet vt; programování vt; MySQL 5; PHP 5; WWW design; dynamické WWW aplikace},
    publisher = {Computer Press},
    isbn = {978-80-251-1813-9},
    year = {2007},
}
');
$configuration->addCitation('d37a537e08d7a4151da4dcf9b3844280', '@book{d37a537e08d7a4151da4dcf9b3844280,
    title = {PHP a XML},
    edition = {1. vyd.},
    language = {cze},
    address = {Praha},
    author = {Kosek, Jiří},
    keywords = {XML vt; software vt; PHP 5; WWW technologie; vývoj softwaru},
    series = {Profesional},
    publisher = {Grada},
    isbn = {978-80-247-1116-4},
    year = {2009},
}
');
$configuration->addCitation('90a4b8da6c464ebad89dca2890ea1dcc', '@misc{90a4b8da6c464ebad89dca2890ea1dcc,
    title = {Skriptovací jazyky pro tvorbu webových aplikací},
    language = {cze},
    author = {Voráček, Jan},
    keywords = {Skriptovací Jazyky ; Webové Aplikace ; Testování Aplikací ; Scripting Languages ; Web Applications ; Software Testing ; Dart ; Typescript ; Javascript},
    abstract = {Diplomová práce se zabývá problematikou skriptovacích jazyků v odvětví webových aplikací. V první části poskytuje obecný náhled do problematiky skriptovacích jazyků. Druhá část je pak věnována jazykům Dart a TypeScript, na které je práce primárně zaměřena. Práce seznamuje čtenáře s různými skriptovacími jazyky, poskytuje drobný náhled do problematiky testování a představuje možnosti použití Dartu a TypeScriptu na serverové části webové aplikace a následně i na její klientské části.},
    publisher = {Univerzita Pardubice},
    year = {2013},
}
');
$configuration->addCitation('e1a5f856ae57fdd7be40f9e5296ca682', '@book{e1a5f856ae57fdd7be40f9e5296ca682,
    title = {PHP okamžitě},
    edition = {1. vyd.},
    language = {cze},
    address = {Brno},
    author = {Hopkins, Callum},
    keywords = {programování vt; příručky; PHP; skriptové jazyky; WWW aplikace; WWW stránky; webové stránky},
    publisher = {Computer Press},
    isbn = {978-80-251-4196-0},
    year = {2014},
}
');
$configuration->addCitation('1d0ea3535d27d2bbdad0f7be3aec281a', '@book{1d0ea3535d27d2bbdad0f7be3aec281a,
    title = {Jak vytvořit úspěšný a výdělečný internetový obchod},
    edition = {1. vyd.},
    language = {cze},
    address = {Brno},
    author = {Sedlák, Mirek},
    keywords = {internet vt; elektronický obchod; elektronická komerce},
    publisher = {Computer Press},
    isbn = {978-80-251-3727-7},
    year = {2012},
}
');
$configuration->addCitation('923d16604426d063c7cecd82b9a7bd8a', '@book{923d16604426d063c7cecd82b9a7bd8a,
      title={E-commerce: Business, Technology, Society},
      author={Laudon, K.C. and Traver, C.G.},
      isbn={9780133938951},
      lccn={2011292760},
      url={https://books.google.cz/books?id=IFAajgEACAAJ},
      year={2016},
      publisher={Pearson}
}
');
$configuration->addCitation('9fe4bb5f22ddff5b36a265957ee0ad98', '@book{9fe4bb5f22ddff5b36a265957ee0ad98,
    title = {E-commerce: elektronické podnikání a koncepce elektronického obchodování},
    edition = {1. vyd.},
    language = {cze},
    address = {Praha},
    author = {Suchánek, Petr},
    keywords = {internet vt; elektronické obchodování ev; internetový obchod; e-business; e-commerce; elektronické podnikání},
    publisher = {Ekopress},
    isbn = {978-80-86929-84-2},
    year = {2012},
}
');
$configuration->addCitation('486db7c727d276f1e0f6de840ebae889', '@book{486db7c727d276f1e0f6de840ebae889,
    title = {E-komerce, internetový a mobil marketing od A do Z},
    edition = {1. vyd.},
    language = {cze},
    address = {Praha},
    author = {Sedláček, Jiří},
    keywords = {internet vt; příručky; elektronický obchod; internetová reklama; internetový marketing},
    publisher = {BEN - technická literatura},
    isbn = {80-7300-195-0},
    year = {2006},
}
');

$configuration->addCitation('0197aa362e5bbed98297e0d31ac1b2db', '@book{0197aa362e5bbed98297e0d31ac1b2db,
    title = {Elektronické obchodování},
    edition = {1. vyd.},
    language = {cze},
    address = {Pardubice: Institut Jana Pernera},
    author = {Švadlenka, Libor a Madleňák, Radovan},
    isbn = {978-80-86530-40-6},
    year = {2007},
}
');

// -- undefined sources
$configuration->addCitation('de49abaf390f6d938dde5a3a2da17181', '@misc{de49abaf390f6d938dde5a3a2da17181,
    title = {Definice statické webové stránky},
    note = "\textit{Myartse.com} [online]. 2014 [cit. 2016-04-01]. Dostupné z: http://www.myartse.com/definice-staticke-webove-stranky/",
}
');

$configuration->addCitation('1d830a0736fd0c9a49aab45046853189', '@misc{1d830a0736fd0c9a49aab45046853189,
    title = {Programovací jazyky},
    note = "\textit{Programování} [online]. 2015 [cit. 2016-04-01]. Dostupné z: http://k-prog.wz.cz/progjaz/",
}
');

$configuration->addCitation('d7a107d421a2e9b36689786d503e7c96', '@misc{d7a107d421a2e9b36689786d503e7c96,
    title = {Programovací paradigma},
    note = "\textit{Naprogramujmi.cz} [online]. Brno-sever: MEDIACENTRUM GROUP, 2013 [cit. 2016-04-01]. Dostupné z: http://naprogramujmi.cz/programovaci-paradigma/",
}
');

(new Convertor($configuration))->convert();