# Routing

This is an early version of the routing utility.
It is part of a bigger project, but can be used as a 
standalone. Requirements are thin as to not pollute
the scope with unnecessary hops.

While it is fully unit tested, it is still considered
to be work in progress.

### Why
As mentioned above this package was born out of the
need for a lightweight, highly flexible routing
package. Existing ones either didn't fit the syntax
requirements or wouldn't fit for other technical
reasons.

### What it is
This routing library is highly customizable and
expandable. It is geared towards PHP8 and makes use
of attributes. However, this is optional as it's 
just one of the ways to define a route. Another goal
was to drive up performance as much as possible,
for which this package will offer different ways of
increasing it.

### What it is not
A full-service solution. This routing package does
not resolve the callbacks given, or the actions defined.
Its sole purpose is to match a request against a defined
route and return the result or throw an exception on
failure.

### What it will be
It will be expanded upon greatly. Some planned features
include:

- Multitude of route resources
    - YAML &check;
    - Database
    - Fake &check;
    - Memory &check;
    - Attributes &check;
    - Plain PHP Files &check;
    
- Multitude of route caches
    - Redis &check;
    - Database
    - Fake &check;
    - APCu &check;
    - Memory &check;
    - Plain PHP Files &check;
    
- Multitude of parsers / matchers
    - Static Parser / Matcher &check;
    - Fake Parser / Matcher &check;
    - Regex Parser / Matcher &check;
    
- Multitude of constraints
    - Common Patterns &check;
    - Fake &check;
    - Segment Defaults &check;
    - Methods &check;
    - Hosts &check;
    - Schemes &check;

- Named routes &check; / LinkBuilder &check;
- Route groups (like Laravel) &check;
    
- RouteMatch object for matches &check;
- Mass matching via collection groups &check;
- Compatible with SymfonyRequests / PSR7 Requests &check;
- Code Improvements / Restructurings
    - Better separation of concerns (&check;)
    - Cleaner classes (&check;)
    - Utility class reworks
    - Better unit tests / less duplication
    
- Routing Factories &check;
- Compile Cache (&check;)


### Versioning
As of right now the project is still work in progress.
No version will be issued until a stable release 
exists. It is still missing too many quality of life
features you would find on your common routing library.

