# Weekly Log (Week 22) 15/02/2024 - 22/02/2024

## Create Environment Setup Instructions for Team Members
Create a set of instructions to get the project up and running on a local machine.
<details>
	<summary>Details</summary>
	The latest instructions can be found in the README for this repo under the heading [How to get up and running](https://github.com/cogilv25/GroupProject?tab=readme-ov-file#how-to-get-up-and-running)
</details>

Completed: **17/02/2024**  

---

## Implement Backend for Login and Registration
Create the backend implementation for Logging in/out and registering using Slim 4 and PHP
<details>
	Backend errors such as Database Down or Incorrect Login Details currently fail silently, user feedback to be implemented.
</details>

Completed: **19/02/2024**

---

## Fix Issues with Database Initialization Script
There were a few issues with the script we created due to the fact it was auto generated, and these needed to be fixed in order for the backend to function.
<details>
	<summary>Breakdown</summary>
	<ul>
		<li>Removed all instances of VISIBLE keyword as MariaDB doesn't support this.</li>
		<li>Added missing days to Scedule.days enum.</li>
		<li><strong>Removed NOT NULL from user.House_houseId as new users will not belong to a house.</strong></li>
		<li>Primary and Foreign keys fixed and simplified.</li>
		<li>Added startTime and stopTime to Task_has_user.</li>
	</ul>
</details>

Completed: **18/02/2024**

---

## Ongoing Research
- Gamification methods
- Slim 4 documentation and usage

---

## Other Business
- Mitchell is doing all his work from college so may have issues getting the project setup locally..?
- Product Backlog
- Setup meeting with Euan
- Myself and Jack felt that this sprint didn't have enough work to do.
- Should we go to 1 sprint per week? I feel this would give us some more flexibility.

---

Weekly Log Completed: **20/02/2024**  