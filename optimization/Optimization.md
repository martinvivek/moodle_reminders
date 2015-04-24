# Optimization

In the light of recent performance issues caused by a 15 mil+ record logstore
serious optimizations must be considered

The logstore must be filtered and duplicated into possibly:
* Another MySQL table __SELECTED OPTION__
* Redis or another NoSQL database
* A temporary file
* A PHP variable

## Logstore Uses
### Find last time course was accessed by teacher
### Determine if discussion was read
### Check how frequently student performed actions

## What to Cache

Find when the oldest currently visible course was created and find logstore records older
than that which also have an action type 'viewed'
