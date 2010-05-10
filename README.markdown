MutateMe
========

A PHP 5.3+ Mutation Testing framework.

Mutation Testing
----------------

According to Wikipedia:

>Mutation testing (or Mutation analysis or Program mutation) is a method of
>software testing, which involves modifying programs' source code or byte code
>in small ways. In short, any tests which pass after code has been mutated
>are considered defective. These so-called mutations, are based on well-defined
>mutation operators that either mimic typical programming errors (such as using
>the wrong operator or variable name) or force the creation of valuable tests
>(such as driving each expression to zero). The purpose is to help the tester
>develop effective tests or locate weaknesses in the test data used for the
>program or in sections of the code that are seldom or never accessed during
>execution.

Prerequisites
-------------

MutateMe requires PHP 5.3.

In addition, MutateMe relies on the PECL runkit extension in order to mutate
source code already in memory. Unfortunately, the current version of runkit
in PHP's official subversion repository is in turns incomplete or buggy. However
an updated runkit is maintained on Github at
[http://github.com/zenovich/runkit](http://github.com/zenovich/runkit "runkit").

To install the runkit extension, the following commands should be sufficient:

`git clone http://github.com/zenovich/runkit.git runkit
cd runkit
phpize
./configure
make
sudo make install`

Once the runkit extension has been installed, you should edit your relevant
php.ini file to add the extension for loading, e.g.

`extension=runkit.so`

Huge thanks to Dmitry Zenovich (zenovich on Github) for this!


Installation
------------

The preferred installation method is via PEAR. At present no PEAR channel
has been provided but this does not prevent a simple install! The simplest
method of installation is:

`git clone git://github.com/padraic/mutateme.git mutateme
cd mutateme
sudo pear install pear.xml`

The above process will install MutateMe as a PEAR library.
