# Benchmarks

How do you know if you're improving if you don't have anything to compare to? Good question. That's what we intend to find out.

## The Idea

The purpose is to provide a baseline and evidence as to the performance of this script. We care a lot about performance and about understanding how changes in our code affects the outcome of the next release. The motivation is the sheer number of regular expressions that need to be processed to come up with the answer to the infamous question:

> can haz we mobile?

Of course, many of you may have ideas about caching and remembering the results of calling the `Mobile_Detect` methods in the session or perhaps some sophisticated caching adapter. That's nice. But we're really just interested in the raw performance here. Caching is sweet icing for later.

## The methodology

Ok, so we want benchmarks. Now what? Let's define a basic methodology with which we will run all of our benchmarks.

### (A) The full spectrum

The full spectrum will be a run of the `isMobile()` and `isTablet()` methods against all known user agents in the `ualist.json` resource file. The statistics gathered will be aggregates over the entire runtime of the tests, rather than a total which would be less meaningful as data gets added to the list. Instead, the following statistics will be generated:

<table>
    <tr>
        <th>Statistic</th>
        <th>Unit of measure</th>
        <th>Description</th>
    </tr>
    <tr>
        <td valign="top"><b>Average runtime</td>
        <td valign="top">Tests/second</td>
        <td valign="top">The average number of tests that can be run per second. Specifically, from initializing <code>Mobile_Detect</code> class to getting the desired result from the <code>isMobile()</code> and <code>isTablet()</code> methods.</td>
    </tr>
    <tr>
        <td valign="top"><b>Median runtime</td>
        <td valign="top">Milliseconds</td>
        <td valign="top">The median runtime per run. See <em>average runtime</em>.</td>
    </tr>
    <tr>
        <td valign="top"><b>Median runtime</td>
        <td valign="top">Milliseconds</td>
        <td valign="top">The median runtime per run. See <em>average runtime</em>.</td>
    </tr>
</table>


### (B) Easier runs

This run is based on choosing specifically those headers which are shortcuts for determining the mobile-ness of a device. Specifically those headers which are tested in `checkHttpHeadersForMobile()` method which is invoked first in the `isMobile` method since it's much less expensive to perform those tests that all the regex necessary in `matchDetectionRulesAgainstUA()` which is invoked after.

The same data is collected as in (A).

