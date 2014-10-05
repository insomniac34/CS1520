create database if not exists test1;

use test1;

create table if not exists all_portal_users (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(100), password VARCHAR(100), email VARCHAR(25), randval INT, role VARCHAR(25), PRIMARY KEY (id));

	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Tyler", MD5("tyler"), "insomniac34@gmail.com", FLOOR(RAND() * 10000) + 10000, "admin");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Mike", MD5("mike"), "tpr11+mike@pitt.edu", FLOOR(RAND() * 10000) + 10000, "admin");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Chris", MD5("john"), "tpr11+chris@pitt.edu", FLOOR(RAND() * 10000) + 10000, "admin");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Bob", MD5("bob"), "tpr11+bob@pitt.edu", FLOOR(RAND() * 10000) + 10000, "admin");	

	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Josh", MD5("josh"), "tpr11+josh@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Dave", MD5("dave"), "tpr11+dave@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Adnan", MD5("adnan"), "tpr11+collin@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Joey", MD5("joey"), "tpr11+joey@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Dylan", MD5("dylan"), "tpr11+dylan@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Keenan", MD5("keenan"), "tpr11+keenan@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Dan", MD5("dan"), "tpr11+dan@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Collin", MD5("collin"), "tpr11+collin@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");
	insert into all_portal_users (id, name, password, email, randval, role) values (NULL, "Ty", MD5("ty"), "tpr11+ty@pitt.edu", FLOOR(RAND() * 10000) + 10000, "user");				

create table if not exists tickets (id INT NOT NULL AUTO_INCREMENT, rec datetime NOT NULL, name VARCHAR(50), email VARCHAR(50), subject VARCHAR(100), tech VARCHAR(50), status varchar(10), PRIMARY KEY (id));
	
	insert into tickets values (NULL, NOW(), "Ty", "insomniac34@gmail.com", "My computer is too awesome", NULL, "open");
	insert into tickets values (NULL, NOW(), "Ty", "tpr11+ty@pitt.edu", "Dropped axe on motherboard.", "Tyler", "open");
	insert into tickets values (NULL, NOW(), "Josh", "tpr11+josh@pitt.edu", "Stuff doesnt work.", NULL, "closed");
	insert into tickets values (NULL, NOW(), "Ty", "tpr11+ty@pitt.edu", "explosions errwhere", NULL, "open");

create table if not exists credentials (username VARCHAR(100), password VARCHAR(100), PRIMARY KEY (username));
	
	insert into credentials values ("tpr11", "1mport@nt");

commit;
