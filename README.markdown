Mutagenesis
========

A PHP 5.3+ Mutation Testing framework.

Mutagenesis is released under a New BSD License.

Mutation Testing
----------------

According to Wikipedia:

>Mutation testing (or Mutation analysis or Program mutation) is a method of
>software testing, which involves modifying programs' source code or byte code
>in small ways. In short, any tests which pass after code has been mutated
>are considered defective. These so-called mutations are based on well-defined
>mutation operators that either mimic typical programming errors (such as using
>the wrong operator or variable name) or force the creation of valuable tests
>(such as driving each expression to zero). The purpose is to help the tester
>develop effective tests or locate weaknesses in the test data used for the
>program or in sections of the code that are seldom or never accessed during
>execution.

Prerequisites
-------------

Mutagenesis requires PHP 5.3 with PHP Tokenizer support enabled.

In addition, Mutagenesis relies on the PECL runkit extension in order to mutate
source code already in memory. Unfortunately, the current version of runkit
in PHP's official subversion repository is in turns incomplete or bugged. However
an updated runkit is maintained on Github at
[http://github.com/zenovich/runkit](http://github.com/zenovich/runkit "runkit").
Mutagenesis requires that this specific version of runkit is used with PHP 5.3.

To install the runkit extension, the following commands should be sufficient:

    git clone http://github.com/zenovich/runkit.git runkit
    cd runkit
    phpize
    ./configure
    make
    sudo make install

Once the runkit extension has been installed, you should edit your relevant
php.ini file to add the extension for loading, e.g.

    extension=runkit.so

Huge thanks to [http://github.com/zenovich](http://github.com/zenovich "Dmitry Zenovich") (zenovich 
on Github) for maintaining this runkit
copy!


Installation
------------

The preferred installation method is via PEAR. At present no PEAR channel
has been provided but this does not prevent a simple install! The simplest
method of installation is:

    git clone git://github.com/padraic/mutagenesis.git mutagenesis
    cd mutagenesis
    sudo pear install pear.xml

The above process will install Mutagenesis as a PEAR library.

Note: If installing from a git clone, you may need to delete any previous
Mutagenesis install via PEAR using:

    sudo pear uninstall Mutagenesis
    
While the git repository tracks code in development, I will add an official
PEAR channel in the near future once a stable release is made.

Note: Mutagenesis supports PHPUnit 3.5 by default, the current stable version.
Earlier versions of PHPUnit may or not work and it is suggested to ensure
PHPUnit is updated prior to using Mutagenesis.

Operation
---------

Mutagenesis is used from the command line using the installed 'mutagenesis' script
or 'mutagenesis.bat' from Windows. It hooks into the underlying test suite for
a library or application by using an adapter. Currently, a PHPUnit adapter is
bundled and used by default.

After an initial test run to ensure all unit tests are passing, Mutagenesis
examines the source code of what is being tested. Based on this examination,
it generates a set of "mutations" (i.e. deliberate errors). For example, it may
locate the boolean value TRUE and generate a mutation to change it to FALSE.
For each generated mutation, the test suite is rerun (via the adapter) with the
mutation applied to the relevant class method using the runkit extension. Each
such test run is performed in a new PHP process.

If the test suite passes, this is interpreted as meaning that the test suite
did not detect the deliberately introduced error. This is referred to as an
"escaped mutant". It demonstrates that your test suite was insufficient to
detect that particular mutation (a possible sign you need more unit tests!).

If the test suite reports a failure, error or exception, this is interpreted as
a successful mutant capture. Your test suite was sufficient to detect the
deliberate error.

After all mutations have been tested, a final report is provided with a small
diff description of each escaped mutant to assist in composing any new unit tests
required.

On a final note, Mutation Testing is a semi-blind process. Not all mutations
introduce true errors. Changing a TRUE to a FALSE where a variable is being
initialised (and is definitely changed later) would not cause a test failure.
Such a reported mutant is a false positive (or ghost). These are unavoidable
but should be examined in context to ensure they are indeed harmless. Updating
source code to remove such false positives will still assist in getting more
relevant Mutation Testing reports in the future.

Command Line Options
--------------------

A typical mutagenesis command is issued with:

    mutagenesis --src="/path/project/library" --tests="/path/project/tests"
    
The basic parameters let you control the directory depth, i.e. which subset of
the source code and/or tests will be utilised. Additional options may be passed
to direct the unit test framework adapter:

* --adapter: The name of the unit test adapter to use; defaults to "phpunit"
* --options: String containing command line switches to pass to the unit test framework
* --timeout: Sets the number of seconds after which a test run is considered timout
* --bootstrap: Sets a bootstrap file to include prior to running tests
* --constraint: PHPUnit class and/or file path for test execution
* --detail-captures: Shows mutation diffs and testing reports for captured mutants

Note: The default timeout is 120 seconds. Any test suite exceeding this should
have a relevant timeout set using --timeout or else all test runs would
timeout. A reported timeout is not a bad thing, it simply means a mutation
may have created a noticeable infinite loop in the source code.

Important: The bootstrap option is essential where your source code relies on
autoloading. Mutagenesis needs to include class files prior to the test adapter
running, so setting a relevant bootstrap prevents include errors. For example,
PHPUnit test suites often use a TestHelper.php or Bootstrap.php file.

For example, imagine we usually employ the following to run some PHPUnit tests:

    phpunit AllTests.php --exclude-group disabled
    
In addition, we use the file TestHelper.php to setup autloading for the tests
(this would normally be included from within AllTests.php manually but Mutagenesis
needs to load it as early as possible).
    
We can pass this to mutagenesis as:

    mutagenesis --src="/path/project/library" --tests="/path/project/tests" \
        --options="--exclude-group disabled" --constraint="AllTests.php" \
        --bootstrap="TestHelper.php"
        
Note: "\\" merely marks a line break for this README. The command should be on
a single line with the \ removed.

This affords a very flexible means of allowing users to use Mutagenesis on narrower
subsets of their test suites.

Understand Mutagenesis Output
--------------------------

Mutagenesis outputs an initial and final report. The initial report is the result
of a pretest, a test run to ensure the test suite is in a passing state before
attempting any mutations. Tests must be in a non-fail state or else mutation
testing cannot be performed (i.e. all mutants would escape!).

The final report renders all escaped mutants with a description of the class,
method and file mutated, along with a diff of the method code that was
changed. Here's a quick exerpt of a mutation test run with escaped mutants
(containing the first escaped example - the remainder are omitted for brevity).

    Mutagenesis 0.5: Mutation Testing for PHP

    All initial checks successful! The mutagenic slime has been activated.

        > PHPUnit 3.4.12 by Sebastian Bergmann.
        > 
        > ............................................................ 60 / 62
        > ..
        > 
        > Time: 0 seconds, Memory: 16.50Mb
        > 
        > OK (62 tests, 156 assertions)
        > 

    Stand by...Mutation Testing commencing.

    ...E........EE........E.EEEEEEE...EEEEE.

    40 Mutants born out of the mutagenic slime!

    16 Mutants escaped; the integrity of your source code may be compromised by the following Mutants:

    1)
    Difference on Idun_Validate_And::isValid() in library/Idun/Validate/And.php
    ===================================================================
    @@ @@
                     $this->_errors = $conditional->getErrors();
    -                return false;
    +                return true;
                 }
             }
             return true;
             
The progress output uses the following markers:

* .: Current mutation was detected by test suite
* E: Current mutation was undetected by test suite
* T: Test suite timed out (see --timeout option)

Supported Mutations
-------------------

Work on Mutagenesis is ongoing, and more mutations will be added over time. Please
refer to the included SupportedMutations file for the current list.

Performance
-----------

Mutation Testing relies on running a test suite repeatedly for each mutation generated.
It is not a fast process, and is better suited to occassional use or in continuous
integration. Mutagenesis has implemented a number of heuristics first utilised by the
Java Jumbler framework in order to speed things up.

1. Exit Early

Mutagenesis only requires a single test in a suite to fail to know when a mutation
has been detected. Therefore, once any test fails, the test suite run is terminated
immediately so we're not pointlessly executed whole test suites unnecessarily.

2. Execute Fastest First

During the initial test suite check, Mutagenesis compiles execution times for each
test case encountered. In subsequent runs, it will execute test cases in order of their
execution times starting with the fastest. This leaves very slow test cases to the very
end thus relying on the probability that any mutation detections will often be detected
earlier by faster tests thus reducing the net time spent in running test suites.

3. Rinse And Repeat

Mutagenesis mutates file by file, meaning that any test case detecting a mutation
has a better change of detecting other mutations on the same file. Therefore,
Mutagenesis first executes the last two tests cases to detect a mutation before
any other test cases to leverage off their probable success.



    


