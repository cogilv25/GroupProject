# Weekly Log (Week 24) 29/02/2024 - 07/03/2024

## Fix Deletion Bugs
Certain tables rely on other tables in the database and so the dependent table rows must be deleted for each row deleted from a table that has dependencies.
<details>
	<summary>Breakdown</summary>
	<ul>
		<li>Delete dependent rules when a room or task is deleted</li>
		<li>Delete dependent task_points when a room or task is deleted</li>
		<li>Delete dependent room, task, rule, rota, task_points when household deleted</li>
	</ul>
</details>

Completed: **29/02/2024**  

---

## Fix Collision Based Bugs
Some tables currently allow multiple rows to have colliding values or values/combinations of values that should be unique per row.
<details>
	<summary>Breakdown</summary>
	<ul>
		<li>Schedule</li>
		<li>Room</li>
		<li>Task</li>
		<li>User Task Rule</li>
		<li>User Room Rule</li>
		<li>Time based rules</li>
	</ul>
</details>

Completed: **02/03/2024**

---

## Initial Rota Generation
Rota Generation without room and task schedules implemented. Also doesn't take into account the fact that we probably want to minimize the number of people cleaning in a single room at once.

Completed: **07/03/2024**

---

## Improve Pre-Database Validation
Currently if a user enters (strings > the database limit) they are just submitted to the database, which will fail, and the user won't get very useful errors. Timeslots beyond 95 will be accepted despite being invalid. There could be similar issues with other datatypes.

Completed: **05/03/2024**

---

## Other Business

---

Weekly Log Completed: **07/03/2024**  