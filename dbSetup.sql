/*
* @Author: smurray12
* @Date:   2020-03-15 16:24:09
* @Last Modified by:   smurray12
* @Last Modified time: 2020-03-18 17:29:36
*/

create table Courses (
	course_id SERIAL NOT NULL,
    description VARCHAR(50),
    time VARCHAR(200),
    PRIMARY KEY(course_id)
);

insert into courses(course_id, description, time) values (311, "lab", "02:11:30");
insert into courses(course_id, description, time) values (320, "lab", "01:15:10");
insert into courses(course_id, description, time) values (330, "lab", "00:41:35");
insert into courses(course_id, description, time) values (370, "lab", "0:57:30");
insert into courses(course_id, description, time) values (460, "lab", "03:12:10");
insert into courses(course_id, description, time) values (400, "lab", "03:12:10");
insert into courses(course_id, description, time) values (310, "study", "00:35:58");
insert into courses(course_id, description, time) values (260, "study", "01:00:58");


--TOTAL TIME BASED ON DESCRIPTION
SELECT description,
    sec_to_time(SUM(time_to_sec(time))) timetotal
    FROM courses
    GROUP BY description;