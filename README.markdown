MutateMe
========

A PHP 5.3+ Mutation Testing framework.

MutateMe is released under a New BSD License.

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

MutateMe requires PHP 5.3.

In addition, MutateMe relies on the PECL runkit extension in order to mutate
source code already in memory. Unfortunately, the current version of runkit
in PHP's official subversion repository is in turns incomplete or bugged. However
an updated runkit is maintained on Github at
[http://github.com/zenovich/runkit](http://github.com/zenovich/runkit "runkit").
MutateMe requires that this specific version of runkit is used with PHP 5.3.

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

Huge thanks to Dmitry Zenovich (zenovich on Github) for maintaining this runkit
copy!


Installation
------------

The preferred installation method is via PEAR. At present no PEAR channel
has been provided but this does not prevent a simple install! The simplest
method of installation is:

    git clone git://github.com/padraic/mutateme.git mutateme
    cd mutateme
    sudo pear install pear.xml

The above process will install MutateMe as a PEAR library.

Note: If installing from a git clone, you may need to delete any previous
MutateMe install via PEAR using:

    sudo pear uninstall MutateMe
    
While the git repository tracks code in development, I hope to add an official
PEAR channel in the near future.

Operation
---------

MutateMe is used from the command line using the installed 'mutateme' script
or 'mutateme.bat' from Windows. It hooks into the underlying test suite for
a library or application by using an adapter. Currently, a PHPUnit adapter is
bundled and used by default.

After an initial test run to ensure all unit tests are passing, MutateMe
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
diff description of each escaped mutant.

It should be noted that, depending on the MutateMe options used, running the
test suite (or a subset thereof) for each mutation can be a time consuming
process (i.e. the time for one test run multiplied by the number of generated
mutants).

On a final note, Mutation Testing is a semi-blind process. Not all mutations
introduce true errors. Changing a TRUE to a FALSE where a variable is being
initialised (and is definitely changed later) would not cause a test failure.
Such a reported mutant is a false positive (or ghost). These are unavoidable
but should be examined in context to ensure they are indeed harmless. Updating
source code to remove such false positives will still assist in getting more
relevant Mutation Testing reports in the future.

Command Line Options
--------------------

A typical mutateme command is issued with:

    mutateme --src="/path/project/library" --tests="/path/project/tests"
    
The basic parameters let you control the directory depth, i.e. which subset of
the source code and/or tests will be utilised. Additional options may be passed
to direct the unit test framework adapter:

* --adapter: The name of the unit test adapter to use; defaults to "phpunit"
* --options: String containing options to pass to the unit test framework's command
* --timeout: Sets the number of seconds after which a test run is considered timout
* --bootstrap: Sets a bootstrap file to include prior to running tests
* --detail-captures: Shows mutation diffs and testing reports for captured mutants

Note: The default timeout is 120 seconds. Any test suite exceeding this should
have a relevant timeout set using --timeout or else all test runs would
timeout. A reported timeout is not a bad thing, it simply means a mutation
may have created a noticeable infinite loop in the source code.

Important: The bootstrap option is essential where your source code relies on
autoloading. MutateMe needs to include class files prior to the test adapter
running, so setting a relevant bootstrap prevents include errors. For example,
PHPUnit test suites often use a TestHelper.php or Bootstrap.php file.

For example, imagine we usually employ the following to run some PHPUnit tests:

    phpunit AllTests.php --exclude-group=disabled
    
In addition, we use the file TestHelper.php to setup autloading for the tests
(this would normally included from with AllTests.php manually but MutateMe
needs to load it as early as possible).
    
We can pass this to mutateme as:

    mutateme --src="/path/project/library" --tests="/path/project/tests" \
        --options="AllTests.php --exclude-group=disabled" \
        --bootstrap="TestHelper.php"
        
Note: "\\" merely marks a line break for this README. The command should be on
a single line with the \ removed.

This affords a very flexible means of allowing users to use MutateMe on narrower
subsets of their test suites.

Understand MutateMe Output
--------------------------

MutateMe outputs an initial and final report. The initial report is the result
of a pretest, a test run to ensure the test suite is in a passing state before
attempting any mutations. Tests must be in a non-fail state or else mutation
testing cannot be performed (i.e. all mutants would escape!).

The final report renders all escaped mutants with a description of the class,
method and file mutated, along with a diff of the method code that was
changed. Here's a quick exerpt of a mutation test run with escaped mutants
(containing the first escaped example - the remainder are omitted for brevity).

    MutateMe 0.5: Mutation Testing for PHP

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
        > EEEEEEEEEE..EEEEE...EEEEE
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

Work on MutateMe is ongoing, and more mutations will be added over time. At
present the following mutations are available (primarily simple operator/value
reversals):

    * BooleanTrue: replace TRUE with FALSE
    * BooleanFalse: replace FALSE with TRUE
    * BooleanAnd: replace && with ||
    * BooleanOr: replace || with &&
    * OperatorAddition: replace + with -
    * OperatorSubtraction: replace - with +
    * OperatorIncrement: replace ++ with --
    * OperatorDecrement: replace -- with ++
    
Obviously, this is just the tip of the iceberg. Mutations will be continually
added now that we have the core framework in a stable working state.

    


