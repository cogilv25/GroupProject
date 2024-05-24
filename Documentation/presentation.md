# Login / Register
- Fairly simple creates a new row in the User table
- Creates a "reasonable default" schedule rows in the user schedule table, this was a bit of a hack to avoid bugs in rota generation but also potentially saves the user some time.

# Dashboard
- Maybe come back to at the end so that the audience knows what all the data be referenced in the explanation is..?
- Rota generation works by collating the user schedules, rooms, tasks, user exemptions, and rules about which tasks are performed in which rooms. We combine the valid rooms and tasks calling each of these a job, then sorts the list of jobs based on the most difficult to allocate then go through the this list and assign the job to the user with the least jobs already assigned to them. Currently if we are unable to assign a task we throw an error and continue.

# 