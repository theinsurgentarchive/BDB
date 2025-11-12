-- MySQL dump 10.19  Distrib 10.3.38-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dchu
-- ------------------------------------------------------
-- Server version	10.3.38-MariaDB-0+deb10u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `promotion_date` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,6,'2025-11-06 22:04:33'),(2,2,'2025-11-06 22:04:33'),(3,13,'2025-11-06 22:04:33'),(4,9,'2025-11-06 22:04:33');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookgenres`
--

DROP TABLE IF EXISTS `bookgenres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookgenres` (
  `book_id` int(11) NOT NULL,
  `genre` varchar(16) NOT NULL,
  PRIMARY KEY (`book_id`,`genre`),
  KEY `genre` (`genre`),
  CONSTRAINT `bookgenres_ibfk_1` FOREIGN KEY (`genre`) REFERENCES `genres` (`genre`) ON DELETE CASCADE,
  CONSTRAINT `bookgenres_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookgenres`
--

LOCK TABLES `bookgenres` WRITE;
/*!40000 ALTER TABLE `bookgenres` DISABLE KEYS */;
INSERT INTO `bookgenres` VALUES (1,'Adventure'),(1,'Sci-Fi'),(2,'Adventure'),(2,'Sci-Fi'),(3,'Adventure'),(3,'Sci-Fi'),(4,'Horror'),(4,'Sci-Fi'),(4,'Thriller'),(5,'Action'),(5,'Adventure'),(5,'Fantasy'),(6,'Action'),(6,'Adventure'),(6,'Fantasy'),(8,'Action'),(8,'Adventure'),(8,'Fantasy'),(9,'Action'),(9,'Adventure'),(9,'Fantasy'),(10,'Action'),(10,'Adventure'),(10,'Fantasy'),(11,'Action'),(11,'Adventure'),(11,'Fantasy'),(12,'Mystery'),(12,'Thriller'),(13,'Adventure'),(13,'Fantasy'),(14,'Historical'),(15,'Adventure'),(15,'Sci-Fi'),(16,'Adventure'),(16,'Fantasy'),(16,'Sci-Fi'),(16,'Thriller'),(17,'Action'),(17,'Fantasy'),(17,'Sci-Fi'),(18,'Action'),(18,'Adventure'),(18,'Fantasy'),(18,'Sci-Fi'),(19,'Adventure'),(19,'Fantasy'),(19,'Sci-Fi'),(20,'Crime'),(20,'Mystery'),(20,'Thriller'),(21,'Fantasy'),(21,'Romance'),(22,'Crime'),(22,'Historical'),(22,'Mystery'),(22,'Romance'),(22,'Thriller'),(23,'Historical'),(24,'Fantasy'),(24,'Romance'),(24,'Sci-Fi'),(25,'Fantasy'),(25,'Romance'),(25,'Sci-Fi'),(26,'Adventure'),(26,'Fantasy'),(27,'Adventure'),(27,'Fantasy'),(28,'Adventure'),(28,'Fantasy'),(29,'Adventure'),(29,'Fantasy'),(30,'Adventure'),(30,'Fantasy'),(31,'Adventure'),(31,'Fantasy'),(31,'Romance'),(32,'Fantasy'),(32,'Historical'),(32,'Romance'),(33,'Fantasy'),(33,'Historical'),(34,'Fantasy'),(34,'Historical'),(34,'Romance'),(35,'Fantasy'),(35,'Romance'),(36,'Fantasy'),(36,'Historical'),(37,'Adventure'),(37,'Crime'),(37,'Fantasy'),(37,'Mystery'),(38,'Classic'),(38,'Dystopian'),(38,'Fantasy'),(38,'Sci-Fi'),(39,'Classic'),(39,'Dystopian'),(39,'Fantasy'),(39,'Sci-Fi'),(40,'Fantasy'),(40,'Sci-Fi'),(41,'Classic'),(41,'Fantasy'),(41,'Sci-Fi'),(42,'Fantasy'),(42,'Sci-Fi'),(43,'Fantasy'),(43,'Sci-Fi'),(44,'Classic'),(44,'Dystopian'),(44,'Romance'),(44,'Sci-Fi'),(45,'Classic'),(45,'Dystopian'),(45,'Fantasy'),(45,'Sci-Fi'),(46,'Classic'),(46,'Crime'),(46,'Mystery'),(46,'Thriller'),(47,'Non-Fiction'),(48,'Classic'),(48,'Historical'),(48,'Non-Fiction'),(49,'Historical'),(49,'Non-Fiction'),(50,'Classic'),(50,'Historical'),(50,'Romance'),(51,'Classic'),(51,'Historical'),(52,'Classic'),(52,'Dystopian'),(53,'Classic'),(53,'Fantasy'),(54,'Adventure'),(54,'Sci-Fi'),(55,'Classic'),(55,'Crime'),(56,'Historical'),(57,'Classic'),(57,'Historical'),(57,'Romance'),(58,'Mystery'),(58,'Thriller'),(59,'Classic'),(59,'Fantasy'),(59,'Horror'),(60,'Adventure'),(60,'Fantasy'),(61,'Adventure'),(61,'Fantasy'),(62,'Classic'),(62,'Historical'),(62,'Romance'),(63,'Dystopian'),(63,'Sci-Fi'),(64,'Classic'),(64,'Fantasy'),(64,'Horror'),(64,'Sci-Fi'),(65,'Classic'),(66,'Adventure'),(66,'Fantasy'),(67,'Adventure'),(67,'Fantasy'),(68,'Adventure'),(68,'Fantasy'),(69,'Dystopian'),(69,'Sci-Fi'),(70,'Adventure'),(70,'Classic'),(70,'Fantasy'),(71,'Adventure'),(71,'Classic'),(71,'Historical'),(72,'Classic'),(72,'Crime'),(73,'Classic'),(73,'Horror'),(74,'Historical'),(74,'Romance'),(75,'Adventure'),(75,'Classic'),(76,'Classic'),(76,'Historical'),(77,'Adventure'),(77,'Fantasy'),(78,'Classic'),(78,'Dystopian'),(78,'Horror'),(78,'Sci-Fi'),(79,'Action'),(79,'Fantasy'),(80,'Action'),(80,'Fantasy'),(81,'Action'),(81,'Fantasy'),(82,'Adventure'),(82,'Fantasy'),(83,'Adventure'),(83,'Fantasy'),(84,'Action'),(84,'Dystopian'),(84,'Romance'),(85,'Action'),(85,'Dystopian'),(85,'Romance'),(86,'Adventure'),(86,'Dystopian'),(86,'Fantasy'),(86,'Romance'),(86,'Sci-Fi'),(87,'Romance'),(88,'Horror'),(88,'Thriller'),(89,'Classic'),(89,'Fantasy'),(89,'Horror'),(89,'Mystery'),(89,'Thriller'),(90,'Classic'),(90,'Historical'),(91,'Classic'),(91,'Dystopian'),(91,'Fantasy'),(91,'Sci-Fi'),(92,'Classic'),(92,'Romance'),(93,'Classic'),(93,'Historical'),(93,'Romance'),(94,'Classic'),(95,'Classic'),(95,'Historical'),(96,'Action'),(96,'Sci-Fi'),(97,'Action'),(97,'Adventure'),(97,'Dystopian'),(97,'Fantasy'),(97,'Romance'),(97,'Sci-Fi'),(98,'Action'),(98,'Adventure'),(98,'Dystopian'),(98,'Fantasy'),(98,'Romance'),(98,'Sci-Fi'),(99,'Action'),(99,'Adventure'),(99,'Dystopian'),(99,'Fantasy'),(99,'Romance'),(99,'Sci-Fi'),(100,'Action'),(100,'Adventure'),(100,'Dystopian'),(100,'Fantasy'),(100,'Mystery'),(100,'Sci-Fi'),(101,'Action'),(101,'Adventure'),(101,'Dystopian'),(101,'Fantasy'),(101,'Mystery'),(101,'Sci-Fi'),(102,'Action'),(102,'Adventure'),(102,'Dystopian'),(102,'Fantasy'),(102,'Sci-Fi'),(103,'Adventure'),(103,'Fantasy'),(104,'Adventure'),(104,'Fantasy'),(105,'Adventure'),(105,'Fantasy'),(106,'Adventure'),(106,'Fantasy'),(107,'Adventure'),(107,'Fantasy'),(108,'Adventure'),(108,'Fantasy'),(109,'Adventure'),(109,'Fantasy'),(110,'Sci-Fi'),(111,'Adventure'),(112,'Mystery'),(112,'Thriller'),(113,'Crime'),(113,'Horror'),(113,'Mystery'),(113,'Thriller'),(114,'Crime'),(114,'Mystery'),(114,'Thriller'),(115,'Crime'),(115,'Mystery'),(115,'Thriller'),(116,'Crime'),(116,'Mystery'),(116,'Thriller'),(117,'Crime'),(117,'Mystery'),(117,'Thriller'),(118,'Crime'),(118,'Mystery'),(118,'Thriller'),(119,'Crime'),(119,'Mystery'),(119,'Thriller'),(120,'Adventure'),(120,'Dystopian'),(120,'Fantasy'),(120,'Romance'),(120,'Sci-Fi'),(121,'Classic'),(121,'Fantasy'),(121,'Horror'),(121,'Sci-Fi'),(121,'Thriller'),(122,'Classic'),(122,'Horror'),(122,'Mystery'),(122,'Thriller'),(123,'Fantasy'),(123,'Horror'),(123,'Mystery'),(123,'Thriller'),(124,'Classic'),(124,'Fantasy'),(124,'Horror'),(124,'Thriller'),(125,'Fantasy'),(125,'Horror'),(125,'Mystery'),(125,'Thriller'),(126,'Fantasy'),(126,'Horror'),(126,'Mystery'),(126,'Thriller'),(127,'Crime'),(127,'Mystery'),(127,'Thriller'),(128,'Crime'),(128,'Mystery'),(128,'Thriller'),(129,'Crime'),(129,'Mystery'),(129,'Thriller'),(130,'Action'),(130,'Adventure'),(130,'Dystopian'),(130,'Fantasy'),(130,'Romance'),(131,'Action'),(131,'Adventure'),(131,'Dystopian'),(131,'Fantasy'),(131,'Romance'),(132,'Action'),(132,'Adventure'),(132,'Dystopian'),(132,'Fantasy'),(132,'Romance'),(133,'Action'),(133,'Adventure'),(133,'Dystopian'),(133,'Fantasy'),(133,'Romance'),(134,'Action'),(134,'Adventure'),(134,'Dystopian'),(134,'Sci-Fi'),(135,'Action'),(135,'Adventure'),(135,'Dystopian'),(135,'Sci-Fi'),(136,'Action'),(136,'Adventure'),(136,'Dystopian'),(136,'Sci-Fi'),(137,'Action'),(137,'Adventure'),(137,'Dystopian'),(137,'Sci-Fi'),(138,'Action'),(138,'Adventure'),(138,'Dystopian'),(138,'Sci-Fi'),(139,'Action'),(139,'Adventure'),(139,'Dystopian'),(139,'Fantasy'),(139,'Romance'),(139,'Sci-Fi'),(140,'Action'),(140,'Adventure'),(140,'Dystopian'),(140,'Fantasy'),(140,'Romance'),(140,'Sci-Fi'),(141,'Action'),(141,'Adventure'),(141,'Dystopian'),(141,'Fantasy'),(141,'Romance'),(141,'Sci-Fi'),(142,'Action'),(142,'Adventure'),(142,'Dystopian'),(142,'Fantasy'),(142,'Romance'),(142,'Sci-Fi');
/*!40000 ALTER TABLE `bookgenres` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dchu`@`localhost`*/ /*!50003 TRIGGER prevent_direct_bookgenre_insert
BEFORE INSERT ON bookgenres FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into booksgenres, use addBook() or formToBook()';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(13) NOT NULL,
  `title` varchar(255) NOT NULL,
  `added` timestamp NULL DEFAULT current_timestamp(),
  `published` date DEFAULT NULL,
  `summary` text NOT NULL,
  `image_path` varchar(512) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `author` varchar(255) NOT NULL,
  PRIMARY KEY (`book_id`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `added_by` (`added_by`),
  CONSTRAINT `books_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `forms` (`form_id`) ON DELETE SET NULL,
  CONSTRAINT `constrain_book_isbn` CHECK (`isbn` regexp '^[0-9]{13}$')
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,'9781442472433','Scythe','2025-11-06 22:04:33','2016-11-22','\"Thou shalt kill\"\n\n A world with no hunger. No disease. No war. No misery. Humanity has conquered all those things, and has even conquered death. Now scythes are the only ones who can end life—and they are commanded to do so, in order to keep the size of the population under control.\n\nCitra and Rowan are chosen to apprentice to a scythe—a role that neither wants. These teens must master the \"art\" of taking life, knowing that the consequence of failure could mean losing their own. They learn living in a perfect world comes only with a heavy price.',NULL,NULL,'Neal Shusterman'),(2,'9781406379532','Thunderhead','2025-11-06 22:04:33','2018-01-09','Rowan has gone rogue, and has taken it upon himself to put the Scythedom through a trial by fire. Literally. In the year since Winter Conclave, he has gone off-grid, and has been striking out against corrupt scythes—not only in MidMerica, but across the entire continent. He is a dark folk hero now—“Scythe Lucifer”—a vigilante taking down corrupt scythes in flames.\n\nCitra, now a junior scythe under Scythe Curie, sees the corruption and wants to help change it from the inside out, but is thwarted at every turn, and threatened by the “new order” scythes. Realizing she cannot do this alone—or even with the help of Scythe Curie and Faraday, she does the unthinkable, and risks being “deadish” so she can communicate with the Thunderhead—the only being on earth wise enough to solve the dire problems of a perfect world. But will it help solve those problems, or simply watch as perfection goes into decline?',NULL,NULL,'Neal Shusterman'),(3,'9781481497060','The Toll','2025-11-06 22:04:33','2019-11-05','It\'s been three years since Rowan and Citra disappeared; since Scythe Goddard came into power; since the Thunderhead closed itself off to everyone but Grayson Tolliver.\n\nIn this pulse-pounding conclusion to New York Times bestselling author Neal Shusterman\'s Arc of a Scythe trilogy, constitutions are tested and old friends are brought back from the dead.',NULL,NULL,'Neal Shusterman'),(4,'9781101974490','The Andromeda Strain','2025-11-06 22:04:33','1969-05-12','The United States government is given a warning by the pre-eminent biophysicists in the country: current sterilization procedures applied to returning space probes may be inadequate to guarantee uncontaminated re-entry to the atmosphere.\n\nTwo years later, seventeen satellites are sent into the outer fringes of space to collect organisms and dust for study. One of them falls to earth, landing in a desolate area of Arizona.\n\nTwelve miles from the landing site, in the town of Piedmont, a shocking discovery is made: the streets are littered with the dead bodies of the town\'s inhabitants, as if they dropped dead in their tracks.',NULL,NULL,'Michael Crichton'),(5,'9780786838653','The Lightning Thief','2025-11-06 22:04:33','2005-07-01','Percy Jackson is a good kid, but he can\'t seem to focus on his schoolwork or control his temper. And lately, being away at boarding school is only getting worse - Percy could have sworn his pre-algebra teacher turned into a monster and tried to kill him. When Percy\'s mom finds out, she knows it\'s time that he knew the truth about where he came from, and that he go to the one place he\'ll be safe. She sends Percy to Camp Half Blood, a summer camp for demigods (on Long Island), where he learns that the father he never knew is Poseidon, God of the Sea. Soon a mystery unfolds and together with his friends—one a satyr and the other the demigod daughter of Athena - Percy sets out on a quest across the United States to reach the gates of the Underworld (located in a recording studio in Hollywood) and prevent a catastrophic war between the gods.',NULL,NULL,'Rick Riordan'),(6,'9781423103349','The Sea of Monsters','2025-11-06 22:04:33','2006-04-01','After a year spent trying to prevent a catastropic war among the Greek gods, Percy Jackson finds his seventh-grade school year unnervingly quiet. His biggest problem is dealing with his new friend, Tyson--a six-foot-three, mentally challenged homeless kid who follows Percy everywhere, making it hard for Percy to have any \"normal\" friends.\n\nBut things don\'t stay quiet for long. Percy soon discovers there is trouble at Camp Half-Blood: The magical borders which protect Half-Blood Hill have been poisoned by a mysterious enemy, and the only safe haven for demigods is on the verge of being overrun by mythological monsters. To save the camp, Percy needs the help of his best friend, Grover, who has been taken prisoner by the Cyclops Polyphemus on an island somewhere in the Sea of Monsters--the dangerous waters Greek heroes have sailed for millenia--only today, the Sea of Monsters goes by a new name...the Bermuda Triangle.\n\nNow Percy and his friends--Grover, Annabeth, and Tyson--must retrieve the Golden Fleece from the Island of the Cyclopes by the end of the summer or Camp Half-Blood will be destroyed. But first, Percy will learn a stunning new secret about his family--one that makes him question whether being claimed as Poseidon\'s son is an honor or simply a cruel joke.',NULL,NULL,'Rick Riordan'),(7,'9781423101482','The Titan\'s Curse','2025-11-06 22:04:33','2007-04-24','It\'s not everyday you find yourself in combat with a half-lion, half-human.\n\nBut when you\'re the son of a Greek god, it happens. And now my friend Annabeth is missing, a goddess is in chains and only five half-blood heroes can join the quest to defeat the doomsday monster.\n\nOh, and guess what? The Oracle has predicted that not all of us will survive...',NULL,NULL,'Rick  Riordan'),(8,'9781423101499','The Battle of the Labyrinth','2025-11-06 22:04:33','2008-03-06','Percy Jackson isn\'t expecting freshman orientation to be any fun. But when a mysterious mortal acquaintance appears at his potential new school, followed by demon cheerleaders, things quickly move from bad to worse.\nIn this fourth installment of the blockbuster series, time is running out as war between the Olympians and the evil Titan lord Kronos draws near. Even the safe haven of Camp Half-Blood grows more vulnerable by the minute as Kronos\'s army prepares to invade its once impenetrable borders. To stop the invasion, Percy and his demigod friends must set out on a quest through the Labyrinth - a sprawling underground world with stunning surprises at every turn.',NULL,NULL,'Rick Riordan'),(9,'9781423101475','The Last Olympian','2025-11-06 22:04:33','2009-05-05','All year the half-bloods have been preparing for battle against the Titans, knowing the odds of victory are grim. Kronos\'s army is stronger than ever, and with every god and half-blood he recruits, the evil Titan\'s power only grows.\n\nWhile the Olympians struggle to contain the rampaging monster Typhon, Kronos begins his advance on New York City, where Mount Olympus stands virtually unguarded. Now it\'s up to Percy Jackson and an army of young demigods to stop the Lord of Time.\n\nIn this momentous final book in the New York Times best-selling series, the long-awaited prophecy surrounding Percy\'s sixteenth birthday unfolds. And as the battle for Western civilization rages on the streets of Manhattan, Percy faces a terrifying suspicion that he may be fighting against his own fate.',NULL,NULL,'Rick Riordan'),(10,'9781368098175','The Chalice of the Gods','2025-11-06 22:04:33','2023-09-26','Percy Jackson, modern-day son of Poseidon, is just trying to get through high school. After saving the world multiple times by battling monsters, Titans, and giants, Percy is now settling in at Alternative High School in New York, where he hopes to finally have a normal senior year.\n\nUnfortunately, the gods aren\'t quite done with him yet. Poseidon breaks the bad news that if Percy expects to get into New Rome University, he will have to fulfill three quests in order to earn the necessary three letters of recommendation from Mount Olympus.\n\nThe first task is to help Ganymede, Zeus\'s cupbearer, retrieve his golden goblet before it falls into the wrong hands. You see, one sip from it can turn a mortal into a god, and Zeus would not be pleased with that result. Can Percy and his friends Grover and Annabeth find the precious cup in time? And if they do, will they be able to resist its special power?\n\nReaders new to Percy Jackson universe and fans who have been awaiting this reunion for more than a decade will delight equally in this latest hilarious take on Greek mythology by the \"storyteller of the gods.\"',NULL,NULL,'Rick Riordan'),(11,'9781368107631','Wrath of the Triple Goddess','2025-11-06 22:04:33','2024-09-24','Percy Jackson, now a high school senior, needs three recommendation letters from the Greek gods in order to get into New Rome University. He earned his first one by retrieving Ganymede\'s chalice. Now the goddess Hecate has offered Percy another “opportunity”—all he has to do is pet sit her mastiff, Hecuba, and her polecat, Gale, over Halloween week while she is away. Piece of cake, right?\n\nPercy, Annabeth, and Grover settle into Hecate\'s seemingly endless mansion and start getting acquainted with the fussy, terrifying animals. The trio has been warned not to touch anything, but while Percy and Annabeth are out at school, Grover can\'t resist drinking a strawberry-flavored potion in the laboratory. It turns him into a giant frenzied goat, and after he rampages through the house, damaging everything in sight, and passes out, Hecuba and Gale escape. Now the friends have to find Hecate\'s pets and somehow restore the house, all before Hecate gets back on Saturday. It\'s going to take luck, demigod wiles, and some old and new friends to hunt down the animals and set things right again.',NULL,NULL,'Rick Riordan'),(12,'9780593640456','The Librarians','2025-11-06 22:04:33','2025-09-30','Murder disrupts the peaceful, predictable daily routine of life for four quirky librarians who must protect their life-altering secrets in the first contemporary mystery from USA Today bestselling author Sherry Thomas.\n\nSometimes a workplace isn\'t just a workplace but a place of safety, understanding, and acceptance. And sometimes murder threatens the sanctity of that beloved refuge....\n\nIn the leafy suburbs of Austin, Texas, a small branch library welcomes the public every day of the week. But the patrons who love the helpful, unobtrusive staff and leave rave reviews on Yelp don\'t always realize that their librarians are human, too.\n\nHazel flees halfway across the world for what she hopes will be a new beginning. Jonathan, a six-foot-four former college football player, has never fit in anywhere else. Astrid tries to forget her heartbreak by immersing herself in work, but the man who ghosted her six months ago is back, promising trouble. And Sophie, who has the most to lose, maintains a careful and respectful distance from her coworkers, but soon that won\'t be enough anymore.\n\nWhen two patrons turn up dead after the library\'s inaugural murder mystery-themed game night, the librarians\' quiet routines come crashing down. Something sinister has stirred, something that threatens every single one of them. And the only way the librarians can save the library—and themselves—is to let go of their secrets, trust one another, and band together....\n\nAll in a day\'s work.',NULL,NULL,'Sherry Thomas'),(13,'9780064404990','The Lion, the Witch and the Wardrobe','2025-11-06 22:04:33','1950-10-16','They open a door and enter a world NARNIA...the land beyond the wardrobe, the secret country known only to Peter, Susan, Edmund, and Lucy...the place where the adventure begins. Lucy is the first to find the secret of the wardrobe in the professor\'s mysterious old house. At first, no one believes her when she tells of her adventures in the land of Narnia. But soon Edmund and then Peter and Susan discover the Magic and meet Aslan, the Great Lion, for themselves. In the blink of an eye, their lives are changed forever.',NULL,NULL,'C.S. Lewis'),(14,'9798491060887','Roots: The Saga of an American Family','2025-11-06 22:04:33','1976-08-17','When he was a boy in Henning, Tennessee, Alex Haley\'s grandmother used to tell him stories about their family—stories that went back to her grandparents, and their grandparents, down through the generations all the way to a man she called \"the African.\" She said he had lived across the ocean near what he called the \"Kamby Bolongo\" and had been out in the forest one day chopping wood to make a drum when he was set upon by four men, beaten, chained and dragged aboard a slave ship bound for Colonial America.\n\nStill vividly remembering the stories after he grew up and became a writer, Haley began to search for documentation that might authenticate the narrative. It took ten years and a half a million miles of travel across three continents to find it, but finally, in an astonishing feat of genealogical detective work, he discovered not only the name of \"the African\"—Kunta Kinte—but the precise location of Juffure, the very village in The Gambia, West Africa, from which he was abducted in 1767 at the age of sixteen and taken on the Lord Ligonier to Maryland and sold to a Virginia planter.\n\nHaley has talked in Juffure with his own African sixth cousins. On September 29, 1967, he stood on the dock in Annapolis where his great-great-great-great-grandfather was taken ashore on September 29, 1767. Now he has written the monumental two-century drama of Kunta Kinte and the six generations who came after him—slaves and freedmen, farmers and blacksmiths, lumber mill workers and Pullman porters, lawyers and architects—and one author.\n\nBut Haley has done more than recapture the history of his own family. As the first black American writer to trace his origins back to their roots, he has told the story of 25,000,000 Americans of African descent. He has rediscovered for an entire people a rich cultural heritage that slavery took away from them, along with their names and their identities. But Roots speaks, finally, not just to blacks, or to whites, but to all people and all races everywhere, for the story it tells is one of the most eloquent testimonials ever written to the indomitability of the human spirit.',NULL,NULL,'Alex Haley'),(15,'9780804139021','The Martian','2025-11-06 22:04:33','2011-09-27','Six days ago, astronaut Mark Watney became one of the first people to walk on Mars.\n\nNow, he\'s sure he\'ll be the first person to die there.\n\nAfter a dust storm nearly kills him and forces his crew to evacuate while thinking him dead, Mark finds himself stranded and completely alone with no way to even signal Earth that he\'s alive—and even if he could get word out, his supplies would be gone long before a rescue could arrive.\n\nChances are, though, he won\'t have time to starve to death. The damaged machinery, unforgiving environment, or plain-old “human error” are much more likely to kill him first.\n\nBut Mark isn\'t ready to give up yet. Drawing on his ingenuity, his engineering skills — and a relentless, dogged refusal to quit — he steadfastly confronts one seemingly insurmountable obstacle after the next. Will his resourcefulness be enough to overcome the impossible odds against him?',NULL,NULL,'Andy Weir'),(16,'9780593135204','Project Hail Mary','2025-11-06 22:04:33','2021-05-04','A lone astronaut. An impossible mission. An ally he never imagined.\n\nRyland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and Earth itself will perish.\n\nExcept that right now, he doesn\'t know that. He can\'t even remember his own name, let alone the nature of his assignment or how to complete it.\n\nAll he knows is that he\'s been asleep for a very, very long time. And he\'s just been awakened to find himself millions of miles from home, with nothing but two corpses for company.\n\nHis crewmates dead, his memories fuzzily returning, Ryland realizes that an impossible task now confronts him. Hurtling through space on this tiny ship, it\'s up to him to puzzle out an impossible scientific mystery—and conquer an extinction-level threat to our species.\n\nAnd with the clock ticking down and the nearest human being light-years away, he\'s got to do it all alone.\n\nOr does he?',NULL,NULL,'Andy Weir'),(17,'9780307887436','Ready Player One','2025-11-06 22:04:33','2011-08-16','IN THE YEAR 2044, reality is an ugly place. The only time teenage Wade Watts really feels alive is when he\'s jacked into the virtual utopia known as the OASIS. Wade\'s devoted his life to studying the puzzles hidden within this world\'s digital confines, puzzles that are based on their creator\'s obsession with the pop culture of decades past and that promise massive power and fortune to whoever can unlock them.\n\nBut when Wade stumbles upon the first clue, he finds himself beset by players willing to kill to take this ultimate prize. The race is on, and if Wade\'s going to survive, he\'ll have to win—and confront the real world he\'s always been so desperate to escape.',NULL,NULL,'Ernest Cline'),(18,'9781524761356','Ready Player Two','2025-11-06 22:04:33','2020-11-24','An unexpected quest. Two worlds at stake. Are you ready?\n\nDays after winning Oasis founder James Halliday\'s contest, Wade Watts makes a discovery that changes everything.\n\nHidden within Halliday\'s vaults, waiting for his heir to find, lies a technological advancement that will once again change the world and make the Oasis a thousand times more wondrous—and addictive—than even Wade dreamed possible.\n\nWith it comes a new riddle, and a new quest—a last Easter egg from Halliday, hinting at a mysterious prize.\n\nAnd an unexpected, impossibly powerful, and dangerous new rival awaits, one who\'ll kill millions to get what he wants.\n\nWade\'s life and the future of the Oasis are again at stake, but this time the fate of humanity also hangs in the balance.\n\nLovingly nostalgic and wildly original as only Ernest Cline could conceive it, Ready Player Two takes us on another imaginative, fun, action-packed adventure through his beloved virtual universe, and jolts us thrillingly into the future once again.',NULL,NULL,'Ernest  Cline'),(19,'9781400052929','The Hitchhiker\'s Guide to the Galaxy','2025-11-06 22:04:33','1979-10-12','Seconds before the Earth is demolished to make way for a galactic freeway, Arthur Dent is plucked off the planet by his friend Ford Prefect, a researcher for the revised edition of The Hitchhiker\'s Guide to the Galaxy who, for the last fifteen years, has been posing as an out-of-work actor.\n\nTogether this dynamic pair begin a journey through space aided by quotes from The Hitchhiker\'s Guide (\"A towel is about the most massively useful thing an interstellar hitchhiker can have\") and a galaxy-full of fellow travelers: Zaphod Beeblebrox--the two-headed, three-armed ex-hippie and totally out-to-lunch president of the galaxy; Trillian, Zaphod\'s girlfriend (formally Tricia McMillan), whom Arthur tried to pick up at a cocktail party once upon a time zone; Marvin, a paranoid, brilliant, and chronically depressed robot; Veet Voojagig, a former graduate student who is obsessed with the disappearance of all the ballpoint pens he bought over the years.\n\nWhere are these pens? Why are we born? Why do we die? Why do we spend so much time between wearing digital watches? For all the answers stick your thumb to the stars. And don\'t forget to bring a towel!',NULL,NULL,'Douglas Adams'),(20,'9781250803825','A Flicker in the Dark','2025-11-06 22:04:33','2022-01-11','When Chloe Davis was twelve, six teenage girls went missing in her small Louisiana town. By the end of the summer, Chloe\'s father had been arrested as a serial killer and promptly put in prison. Chloe and the rest of her family were left to grapple with the truth and try to move forward while dealing with the aftermath.\n\nNow 20 years later, Chloe is a psychologist in private practice in Baton Rouge and getting ready for her wedding. She finally has a fragile grasp on the happiness she\'s worked so hard to get. Sometimes, though, she feels as out of control of her own life as the troubled teens who are her patients. And then a local teenage girl goes missing, and then another, and that terrifying summer comes crashing back. Is she paranoid, and seeing parallels that aren\'t really there, or for the second time in her life, is she about to unmask a killer?\n\nIn a debut novel that has already been optioned for a limited series by actress Emma Stone and sold to a dozen countries around the world, Stacy Willingham has created an unforgettable character in a spellbinding thriller that will appeal equally to fans of Gillian Flynn and Karin Slaughter.',NULL,NULL,'Stacy Willingham'),(21,'9781665954884','Powerless','2025-11-06 22:04:33','2023-01-31','She is the very thing he\'s spent his whole life hunting.\nHe is the very thing she\'s spent her whole life pretending to be.\n\nOnly the extraordinary belong in the kingdom of Ilya—the exceptional, the empowered, the Elites.\n\nThe powers these Elites have possessed for decades were graciously gifted to them by the Plague, though not all were fortunate enough to both survive the sickness and reap the reward. Those born Ordinary are just that—ordinary. And when the king decreed that all Ordinaries be banished in order to preserve his Elite society, lacking an ability suddenly became a crime—making Paedyn Gray a felon by fate and a thief by necessity.\n\nSurviving in the slums as an Ordinary is no simple task, and Paedyn knows this better than most. Having been trained by her father to be overly observant since she was a child, Paedyn poses as a Psychic in the crowded city, blending in with the Elites as best she can in order to stay alive and out of trouble. Easier said than done.\n\nWhen Paeydn unsuspectingly saves one of Ilyas princes, she finds herself thrown into the Purging Trials. The brutal competition exists to showcase the Elites\' powers—the very thing Paedyn lacks. If the Trials and the opponents within them don\'t kill her, the prince she\'s fighting feelings for certainly will if he discovers what she is—completely Ordinary.',NULL,NULL,'Lauren Roberts'),(22,'9781668078181','Broken Country','2025-11-06 22:04:33','2025-03-04','Beth and her gentle, kind husband Frank are happily married, but their relationship relies on the past staying buried. But when Beth\'s brother-in-law shoots a dog going after their sheep, Beth doesn\'t realize that the gunshot will alter the course of their lives. For the dog belonged to none other than Gabriel Wolfe, the man Beth loved as a teenager—the man who broke her heart years ago. Gabriel has returned to the village with his young son Leo, a boy who reminds Beth very much of her own son, who died in a tragic accident.\n\nAs Beth is pulled back into Gabriel\'s life, tensions around the village rise and dangerous secrets and jealousies from the past resurface, this time with deadly consequences. Beth is forced to make a choice between the woman she once was, and the woman she has become.\n\nA sweeping love story with the pace and twists of a thriller, Broken Country is a novel of simmering passion, impossible choices, and explosive consequences that toggles between the past and present to explore the far-reaching legacy of first love.',NULL,NULL,'Clare Leslie Hall'),(23,'9780063291324','The Great Divide','2025-11-06 22:04:33','2024-03-05','It is said that the Canal will be the greatest feat of engineering in history. But first, it must be built. Ada Bunting, a bold sixteen-year-old from Barbados, arrives alone in Panama as a stowaway alongside thousands of other West Indians seeking work in the grand building project of the Canal. Francisco, a local fisherman, resents the foreign nations clamouring for a slice of his country, but nothing is more upsetting for him than his son Omar\'s decision to work as a digger. For Omar, whose upbringing was quiet and lonely, this job offers a chance to finally find connection and independence. Scientist John Oswald has come from further afield. He has journeyed to Panama in pursuit of one goal: eliminating malaria. But everything hangs in the balance as his wife Marian falls ill herself. When John witnesses an act of bravery and compassion from Ada one day, he hires her on the spot as a caregiver for his wife. This fateful decision sets in motion a sweeping tale of ambition, loyalty, and sacrifice. Breathtaking and impossible to put down, The Great Divide explores the lives of the labourers, fishmongers, journalists, protesters, doctors and soothsayers who lived alongside the construction of the Canal - those rarely acknowledged by history even as they carved out its course.',NULL,NULL,'Cristina Henríquez'),(24,'9780062652850','Heart of Iron','2025-11-06 22:04:33','2018-02-27','Seventeen-year-old Ana is a scoundrel by nurture and an outlaw by nature. Found as a child drifting through space with a sentient android called D09, Ana was saved by a fearsome space captain and the grizzled crew she now calls family. But D09—one of the last remaining illegal Metals—has been glitching, and Ana will stop at nothing to find a way to fix him.\n\nAna\'s desperate effort to save D09 leads her on a quest to steal the coordinates to a lost ship that could offer all the answers. But at the last moment, a spoiled Ironblood boy beats Ana to her prize. He has his own reasons for taking the coordinates, and he doesn\'t care what he\'ll sacrifice to keep them.\n\nWhen everything goes wrong, she and the Ironblood end up as fugitives on the run. Now their entire kingdom is after them—and the coordinates—and not everyone wants them captured alive.\n\nWhat they find in a lost corner of the universe will change all their lives—and unearth dangerous secrets. But when a darkness from Ana\'s past returns, she must face an impossible choice: does she protect a kingdom that wants her dead or save the Metal boy she loves?',NULL,NULL,'Ashley Poston'),(25,'9780062847355','Soul of Stars','2025-11-06 22:04:33','2019-07-23','Once Ana was an orphaned space outlaw. Then she was the Empress of the Iron Kingdom. Now, thought dead by most of the galaxy after she escaped from the dark AI program called the HIVE, Ana is desperate for a way to save Di from the HIVE\'s evil clutches and take back her kingdom.\n\nAna\'s only option is to find Starbright, the one person who hacked into the HIVE and lived to tell the tale. But when Ana\'s desperation costs the crew of the Dossier a terrible price, Ana and her friends are sent spiraling through the most perilous reaches of the Iron Kingdom to stop the true arbiter of evil in her an ancient world-ending deity called the Great Dark.\n\nTheir journey will take their sharp-witted pilot, Jax, to the home he never wanted to return to and the dangerous fate he left behind. And when Robb finds out who Jax really is, he must contend with his own feelings for the boy he barely knows, and question whether he truly belongs with this group of outcasts.\n\nWhen facing the worst odds, can Ana and her crew of misfits find a way to stop the Great Dark once and for all?',NULL,NULL,'Ashley Poston'),(26,'9780756404079','The Name of the Wind','2025-11-06 22:04:33','2007-03-27','Kvothe recounts his life—from a troupe child to the most notorious arcanist—seeking the truth behind legend.',NULL,NULL,'Patrick Rothfuss'),(27,'9780756404734','The Wise Man\'s Fear','2025-11-06 22:04:33','2011-03-01','Kvothe continues his education, journeys far from the University, and confronts the Chandrian\'s shadow.',NULL,NULL,'Patrick Rothfuss'),(28,'9780765311788','Mistborn: The Final Empire','2025-11-06 22:04:33','2006-07-17','A street thief joins a crew to topple an immortal tyrant using allomancy—magic fueled by metals.',NULL,NULL,'Brandon Sanderson'),(29,'9780765326355','The Way of Kings','2025-11-06 22:04:33','2010-08-31','On the shattered world of Roshar, a soldier, a scholar, and a prince shape the fate of nations.',NULL,NULL,'Brandon Sanderson'),(30,'9780765326362','Words of Radiance','2025-11-06 22:04:33','2014-03-04','Oaths are sworn and ancient powers return as war and storms converge on Roshar.',NULL,NULL,'Brandon Sanderson'),(31,'9781627792127','Six of Crows','2025-11-06 22:04:33','2015-09-29','A crew of outcasts attempts an impossible heist in a deadly, magic-tinged city.',NULL,NULL,'Leigh Bardugo'),(32,'9780385534635','The Night Circus','2025-11-06 22:04:33','2011-09-13','A mysterious circus becomes the stage for a duel between magicians and a star-crossed romance.',NULL,NULL,'Erin Morgenstern'),(33,'9780316556347','Circe','2025-11-06 22:04:33','2018-04-10','The witch of Aiaia finds her power and voice among gods and mortals.',NULL,NULL,'Madeline Miller'),(34,'9780062060624','The Song of Achilles','2025-11-06 22:04:33','2011-09-20','Patroclus narrates his bond with Achilles from boyhood to the Trojan War.',NULL,NULL,'Madeline Miller'),(35,'9781635570281','The Priory of the Orange Tree','2025-11-06 22:04:33','2019-02-26','Queens, mages, and dragon riders rally as an ancient wyrm threatens the world.',NULL,NULL,'Samantha Shannon'),(36,'9780062662590','The Poppy War','2025-11-06 22:04:33','2018-05-01','A war orphan\'s rise through a brutal academy reveals destructive shamanic power.',NULL,NULL,'R.F. Kuang'),(37,'9780593725429','The Lies of Locke Lamora','2025-11-06 22:04:33','2006-06-27','Gentleman Bastards con and steal their way through the criminal underworld of Camorr.',NULL,NULL,'Scott Lynch'),(38,'9780441007462','Neuromancer','2025-11-06 22:04:33','1984-07-01','A washed-up hacker is hired for a last job that pulls him through cyberspace and corporate intrigue.',NULL,NULL,'William Gibson'),(39,'9780593599730','Snow Crash','2025-11-06 22:04:33','1992-06-01','A samurai-sword courier and a skateboard Kourier uncover a memetic virus in the Metaverse.',NULL,NULL,'Neal Stephenson'),(40,'9780765382030','The Three-Body Problem','2025-11-06 22:04:33','2008-01-01','First contact intertwines with Cultural Revolution secrets and a cosmic game of survival.',NULL,NULL,'Cixin Liu'),(41,'9780441007318','The Left Hand of Darkness','2025-11-06 22:04:33','1969-03-01','On a planet of ambisexual humans, an envoy must bridge culture and politics to survive.',NULL,NULL,'Ursula K. Le Guin'),(42,'9780060853983','Good Omens: The Nice and Accurate Prophecies of Agnes Nutter, Witch','2025-11-06 22:04:33','1990-05-01','An angel and a demon team up to avert the apocalypse and misplace the Antichrist.',NULL,NULL,'Terry Pratchett'),(43,'9780062472106','American Gods','2025-11-06 22:04:33','2001-06-19','A man is drawn into a war between old gods and new across a mythic America.',NULL,NULL,'Neil Gaiman'),(44,'9781400078776','Never Let Me Go','2025-11-06 22:04:33','2005-04-05','At an English boarding school, three friends uncover the unsettling purpose behind their existence.',NULL,NULL,'Kazuo Ishiguro'),(45,'9780385732550','The Giver','2025-11-06 22:04:33','1993-04-26','A boy learns the cost of a perfectly controlled, pain-free society when he inherits memories of the past.',NULL,NULL,'Lois Lowry'),(46,'9780451528018','The Hound of the Baskervilles','2025-11-06 22:04:33','1902-04-01','A detective novel featuring Sherlock Holmes investigating a legendary beast on the moors.',NULL,NULL,'Arthur Conan Doyle'),(47,'9780399590504','Educated','2025-11-06 22:04:33','2018-02-20','A memoir about a woman who grows up in a strict and abusive household and eventually escapes to pursue education.',NULL,NULL,'Tara Westover'),(48,'9780553296983','The Diary of a Young Girl','2025-11-06 22:04:33','1947-06-25','The wartime diary of Anne Frank, chronicling her life in hiding during World War II.',NULL,NULL,'Anne Frank'),(49,'9781524763138','Becoming','2025-11-06 22:04:33','2018-11-13','The inspiring memoir of former First Lady Michelle Obama.',NULL,NULL,'Michelle Obama'),(50,'9780147514011','Little Women','2025-11-06 22:04:33','1868-09-30','A heartwarming novel about the lives and growth of the March sisters.',NULL,NULL,'Louisa May Alcott'),(51,'9780061120084','To Kill a Mockingbird','2025-11-06 22:04:33','1960-07-11','A novel about racial injustice and moral growth in the Deep South.',NULL,NULL,'Harper Lee'),(52,'9780451524935','1984','2025-11-06 22:04:33','1949-06-08','A dystopian novel exploring totalitarianism and surveillance.',NULL,NULL,'George Orwell'),(53,'9780553213690','The Metamorphosis','2025-11-06 22:04:33','1915-10-01','A man wakes up one day transformed into a giant insect.',NULL,NULL,'Franz Kafka'),(54,'9780441172719','Dune','2025-11-06 22:04:33','1965-08-01','A young noble becomes embroiled in a struggle for control of a desert planet and its valuable spice.',NULL,NULL,'Frank Herbert'),(55,'9780679720201','The Stranger','2025-11-06 22:04:33','1942-04-01','A man\'s detached response to life and death challenges societal norms.',NULL,NULL,'Albert Camus'),(56,'9780375842207','The Book Thief','2025-11-06 22:04:33','2005-03-14','A young girl steals books in Nazi Germany while the world around her burns.',NULL,NULL,'Markus Zusak'),(57,'9781503290563','Pride and Prejudice','2025-11-06 22:04:33','1813-01-28','A witty romantic drama about love, class, and misunderstanding in Regency England.',NULL,NULL,'Jane Austen'),(58,'9780307474278','The Da Vinci Code','2025-11-06 22:04:33','2003-03-18','A symbologist uncovers a secret society while investigating a murder in the Louvre.',NULL,NULL,'Dan Brown'),(59,'9780486411095','Dracula','2025-11-06 22:04:33','1897-05-26','A gothic tale of the infamous vampire Count Dracula\'s arrival in England.',NULL,NULL,'Bram Stoker'),(60,'9780590353427','Harry Potter and the Sorcerer\'s Stone','2025-11-06 22:04:33','1997-06-26','A young wizard discovers his magical heritage and attends Hogwarts School.',NULL,NULL,'J.K. Rowling'),(61,'9780547928227','The Hobbit','2025-11-06 22:04:33','1937-09-21','A hobbit embarks on a quest to reclaim a stolen treasure guarded by a dragon.',NULL,NULL,'J.R.R. Tolkien'),(62,'9780743273565','The Great Gatsby','2025-11-06 22:04:33','1925-04-10','A mysterious millionaire pursues the love of his life in Jazz Age America.',NULL,NULL,'F. Scott Fitzgerald'),(63,'9780060850524','Brave New World','2025-11-06 22:04:33','1932-01-01','A futuristic society explores the consequences of technological control and conformity.',NULL,NULL,'Aldous Huxley'),(64,'9780486282114','Frankenstein','2025-11-06 22:04:33','1818-01-01','A scientist creates life but faces dire consequences when his creation turns on him.',NULL,NULL,'Mary Shelley'),(65,'9780316769488','The Catcher in the Rye','2025-11-06 22:04:33','1951-07-16','A disillusioned teenager wanders New York City, reflecting on alienation and youth.',NULL,NULL,'J.D. Salinger'),(66,'9780547928210','The Lord of the Rings: The Fellowship of the Ring','2025-11-06 22:04:33','1954-07-29','A hobbit and his companions begin a quest to destroy the One Ring.',NULL,NULL,'J.R.R. Tolkien'),(67,'9780547928203','The Lord of the Rings: The Two Towers','2025-11-06 22:04:33','1954-11-11','The Fellowship is broken, and new alliances form against Sauron\'s forces.',NULL,NULL,'J.R.R. Tolkien'),(68,'9780547928197','The Lord of the Rings: The Return of the King','2025-11-06 22:04:33','1955-10-20','The final battle for Middle-earth unfolds as Frodo nears Mount Doom.',NULL,NULL,'J.R.R. Tolkien'),(69,'9781451673319','Fahrenheit 451','2025-11-06 22:04:33','1953-10-19','In a world where books are banned, one man begins to question his society.',NULL,NULL,'Ray Bradbury'),(70,'9780061122415','The Alchemist','2025-11-06 22:04:33','1988-04-01','A shepherd journeys to find treasure and discovers his personal legend.',NULL,NULL,'Paulo Coelho'),(71,'9781503280786','Moby Dick','2025-11-06 22:04:33','1851-10-18','Captain Ahab obsessively hunts the white whale that cost him his leg.',NULL,NULL,'Herman Melville'),(72,'9780486415871','Crime and Punishment','2025-11-06 22:04:33','1866-01-01','A young man commits murder and faces moral turmoil and redemption.',NULL,NULL,'Fyodor Dostoevsky'),(73,'9780141439570','The Picture of Dorian Gray','2025-11-06 22:04:33','1890-07-01','A man remains eternally young while his portrait reflects his sins.',NULL,NULL,'Oscar Wilde'),(74,'9780451419432','Les Misérables','2025-11-06 22:04:33','1862-04-03','An ex-convict seeks redemption amid revolutionary France.',NULL,NULL,'Victor Hugo'),(75,'9780140449266','The Count of Monte Cristo','2025-11-06 22:04:33','1844-08-28','A man wrongly imprisoned seeks revenge against those who betrayed him.',NULL,NULL,'Alexandre Dumas'),(76,'9781594631931','The Kite Runner','2025-11-06 22:04:33','2003-05-29','A story of friendship, betrayal, and redemption in Afghanistan.',NULL,NULL,'Khaled Hosseini'),(77,'9780156027328','Life of Pi','2025-11-06 22:04:33','2001-09-11','A boy stranded on a lifeboat with a tiger must fight to survive.',NULL,NULL,'Yann Martel'),(78,'9780307387899','The Road','2025-11-06 22:04:33','2006-09-26','A father and son journey through a post-apocalyptic wasteland.',NULL,NULL,'Cormac McCarthy'),(79,'9780553103540','A Game of Thrones','2025-11-06 22:04:33','1996-08-06','Noble families battle for power in a medieval fantasy world.',NULL,NULL,'George R.R. Martin'),(80,'9780553108033','A Clash of Kings','2025-11-06 22:04:33','1998-11-16','The realm is torn apart by war as alliances shift and betrayals rise.',NULL,NULL,'George R.R. Martin'),(81,'9780553106633','A Storm of Swords','2025-11-06 22:04:33','2000-08-08','Wars and betrayals intensify as the Iron Throne\'s fate is tested.',NULL,NULL,'George R.R. Martin'),(82,'9780553801507','A Feast for Crows','2025-11-06 22:04:33','2005-11-08','The war\'s aftermath breeds new schemes and uneasy alliances.',NULL,NULL,'George R.R. Martin'),(83,'9780553801477','A Dance with Dragons','2025-11-06 22:04:33','2011-07-12','As winter approaches, old enemies return and new powers rise.',NULL,NULL,'George R.R. Martin'),(84,'9780439023481','The Hunger Games','2025-11-06 22:04:33','2008-09-14','A girl fights to survive a deadly televised competition.',NULL,NULL,'Suzanne Collins'),(85,'9780439023498','Catching Fire','2025-11-06 22:04:33','2009-09-01','Katniss becomes a symbol of rebellion against the Capitol.',NULL,NULL,'Suzanne Collins'),(86,'9780439023511','Mockingjay','2025-11-06 22:04:33','2010-08-24','The rebellion reaches its climax as the Capitol falls.',NULL,NULL,'Suzanne Collins'),(87,'9780525478812','The Fault in Our Stars','2025-11-06 22:04:33','2012-01-10','Two teens with cancer fall in love and confront mortality.',NULL,NULL,'John Green'),(88,'9780307743657','The Shining','2025-11-06 22:04:33','1977-01-28','A man\'s sanity unravels in an isolated haunted hotel.',NULL,NULL,'Stephen King'),(89,'9781501142970','It','2025-11-06 22:04:33','1986-09-15','A shape-shifting evil haunts the children of Derry, Maine.',NULL,NULL,'Stephen King'),(90,'9780140385724','The Outsiders','2025-11-06 22:04:33','1967-04-24','Teen gangs navigate class conflict and identity in Oklahoma.',NULL,NULL,'S.E. Hinton'),(91,'9780451526342','Animal Farm','2025-11-06 22:04:33','1945-08-17','Farm animals overthrow their master and establish a corrupt regime.',NULL,NULL,'George Orwell'),(92,'9780142437209','Jane Eyre','2025-11-06 22:04:33','1847-10-16','An orphaned governess finds love and independence against the odds.',NULL,NULL,'Charlotte Brontë'),(93,'9780141439556','Wuthering Heights','2025-11-06 22:04:33','1847-12-01','A dark tale of passion and revenge on the Yorkshire moors.',NULL,NULL,'Emily Brontë'),(94,'9780060837020','The Bell Jar','2025-11-06 22:04:33','1963-01-14','A young woman\'s descent into depression mirrors societal pressures.',NULL,NULL,'Sylvia Plath'),(95,'9780156028356','The Color Purple','2025-11-06 22:04:33','1982-10-01','An African-American woman\'s struggle for freedom and self-worth in the 20th century South.',NULL,NULL,'Alice Walker'),(96,'9780812550702','Ender\'s Game','2025-11-06 22:04:33','1985-01-15','A young genius is trained through war games to defend humanity from aliens.',NULL,NULL,'Orson Scott Card'),(97,'9780062024039','Divergent','2025-11-06 22:04:33','2011-04-25','In a divided dystopian society, a girl discovers she doesn\'t fit into any faction.',NULL,NULL,'Veronica Roth'),(98,'9780062024046','Insurgent','2025-11-06 22:04:33','2012-05-01','Tris fights to uncover the truth about her society and her identity.',NULL,NULL,'Veronica Roth'),(99,'9780062024060','Allegiant','2025-11-06 22:04:33','2013-10-22','The conclusion to the Divergent trilogy as the truth about their world is revealed.',NULL,NULL,'Veronica Roth'),(100,'9780385737952','The Maze Runner','2025-11-06 22:04:33','2009-10-06','Teens wake up in a deadly maze with no memory of who they are.',NULL,NULL,'James Dashner'),(101,'9780385738768','The Scorch Trials','2025-11-06 22:04:33','2010-09-18','The survivors face a new desert challenge with deadly obstacles.',NULL,NULL,'James Dashner'),(102,'9780385738775','The Death Cure','2025-11-06 22:04:33','2011-10-11','The final battle against WICKED and the truth of the maze is revealed.',NULL,NULL,'James Dashner'),(103,'9780812511819','The Eye of the World','2025-11-06 22:04:33','1990-01-15','A young man is swept into a vast battle against the Dark One.',NULL,NULL,'Robert Jordan'),(104,'9780812517729','The Great Hunt','2025-11-06 22:04:33','1990-11-15','The heroes pursue a stolen horn and battle dark forces.',NULL,NULL,'Robert Jordan'),(105,'9780812513714','The Dragon Reborn','2025-11-06 22:04:33','1991-10-15','Rand al\'Thor begins to accept his destiny as the Dragon Reborn.',NULL,NULL,'Robert Jordan'),(106,'9780375826689','Eragon','2025-11-06 22:04:33','2003-06-26','A farm boy discovers a dragon egg and becomes a Dragon Rider.',NULL,NULL,'Christopher Paolini'),(107,'9780375840401','Eldest','2025-11-06 22:04:33','2005-08-23','Eragon continues his training as war spreads across Alagaësia.',NULL,NULL,'Christopher Paolini'),(108,'9780375826726','Brisingr','2025-11-06 22:04:33','2008-09-20','Eragon faces new challenges and revelations about his destiny.',NULL,NULL,'Christopher Paolini'),(109,'9780375846311','Inheritance','2025-11-06 22:04:33','2011-11-08','The epic conclusion of the Inheritance Cycle as Eragon faces Galbatorix.',NULL,NULL,'Christopher Paolini'),(110,'9780553448122','Artemis','2025-11-06 22:04:33','2017-11-14','A smuggler on the Moon gets caught in a dangerous conspiracy.',NULL,NULL,'Andy Weir'),(111,'9780345538994','The Lost World','2025-11-06 22:04:33','1995-09-19','A second island of dinosaurs sparks a new deadly adventure.',NULL,NULL,'Michael Crichton'),(112,'9780312924584','The Silence of the Lambs','2025-11-06 22:04:33','1988-05-19','An FBI trainee seeks help from a brilliant cannibal to catch a serial killer.',NULL,NULL,'Thomas Harris'),(113,'9780440224679','Hannibal','2025-11-06 22:04:33','1999-06-08','The hunt for Hannibal Lecter leads to a shocking reunion.',NULL,NULL,'Thomas Harris'),(114,'9780307588371','Gone Girl','2025-11-06 22:04:33','2012-06-05','A husband becomes the prime suspect when his wife goes missing.',NULL,NULL,'Gillian Flynn'),(115,'9780307341556','Sharp Objects','2025-11-06 22:04:33','2006-09-26','A reporter returns to her hometown to cover a series of brutal murders.',NULL,NULL,'Gillian Flynn'),(116,'9780307341570','Dark Places','2025-11-06 22:04:33','2009-05-05','A woman revisits the massacre of her family and uncovers dark truths.',NULL,NULL,'Gillian Flynn'),(117,'9780307454546','The Girl with the Dragon Tattoo','2025-11-06 22:04:33','2005-08-01','A journalist and hacker investigate a decades-old disappearance.',NULL,NULL,'Stieg Larsson'),(118,'9780307454553','The Girl Who Played with Fire','2025-11-06 22:04:33','2006-06-01','Lisbeth Salander becomes the prime suspect in a triple murder.',NULL,NULL,'Stieg Larsson'),(119,'9780307454560','The Girl Who Kicked the Hornet\'s Nest','2025-11-06 22:04:33','2007-05-01','Lisbeth faces her enemies and seeks justice once and for all.',NULL,NULL,'Stieg Larsson'),(120,'9781338635171','The Hunger Games: The Ballad of Songbirds and Snakes','2025-11-06 22:04:33','2020-05-19','Coriolanus Snow mentors a tribute in the 10th Hunger Games.',NULL,NULL,'Suzanne Collins'),(121,'9780307743688','The Stand','2025-11-06 22:04:33','1978-09-01','A deadly plague wipes out most of humanity, sparking a battle between good and evil.',NULL,NULL,'Stephen King'),(122,'9780450417399','Misery','2025-11-06 22:04:33','1987-06-08','An author is held captive by his \"number one fan.\"',NULL,NULL,'Stephen King'),(123,'9781501156700','Pet Sematary','2025-11-06 22:04:33','1983-11-14','A family discovers a burial ground with the power to bring the dead back.',NULL,NULL,'Stephen King'),(124,'9780307743664','Carrie','2025-11-06 22:04:33','1974-04-05','A bullied girl with telekinetic powers seeks revenge at prom.',NULL,NULL,'Stephen King'),(125,'9780307743671','Salem\'s Lot','2025-11-06 22:04:33','1975-10-17','A writer returns to his hometown to find it overrun by vampires.',NULL,NULL,'Stephen King'),(126,'9781476727653','Doctor Sleep','2025-11-06 22:04:33','2013-09-24','The grown-up Danny Torrance faces new horrors tied to his past.',NULL,NULL,'Stephen King'),(127,'9780062678416','The Woman in the Window','2025-11-06 22:04:33','2018-01-02','An agoraphobic woman witnesses a crime across the street—or does she?',NULL,NULL,'A.J. Finn'),(128,'9780062060563','Before I Go to Sleep','2025-11-06 22:04:33','2011-06-14','A woman wakes up each day with no memory of her past.',NULL,NULL,'S.J. Watson'),(129,'9780735221109','The Couple Next Door','2025-11-06 22:04:33','2016-08-23','A dinner party leads to a missing baby and dark secrets revealed.',NULL,NULL,'Shari Lapena'),(130,'9781595148032','An Ember in the Ashes','2025-11-06 22:04:33','2015-04-28','A slave girl and a soldier cross paths in an oppressive empire.',NULL,NULL,'Sabaa Tahir'),(131,'9781101998885','A Torch Against the Night','2025-11-06 22:04:33','2016-08-30','Laia and Elias flee the empire as rebellion brews.',NULL,NULL,'Sabaa Tahir'),(132,'9780448494500','A Reaper at the Gates','2025-11-06 22:04:33','2018-06-12','The war for the Empire intensifies as Laia faces her destiny.',NULL,NULL,'Sabaa Tahir'),(133,'9780448494531','A Sky Beyond the Storm','2025-11-06 22:04:33','2020-12-01','The final battle for the fate of the world unfolds.',NULL,NULL,'Sabaa Tahir'),(134,'9780345539786','Red Rising','2025-11-06 22:04:33','2014-01-28','A miner infiltrates the ruling elite to ignite a revolution on Mars.',NULL,NULL,'Pierce Brown'),(135,'9780345539816','Golden Son','2025-11-06 22:04:33','2015-01-06','Darrow climbs the ranks of the Golds while his rebellion grows.',NULL,NULL,'Pierce Brown'),(136,'9780345539847','Morning Star','2025-11-06 22:04:33','2016-02-09','The rebellion reaches its climax in a brutal interplanetary war.',NULL,NULL,'Pierce Brown'),(137,'9780425285954','Dark Age','2025-11-06 22:04:33','2019-07-30','New power struggles and betrayals erupt as humanity teeters on chaos.',NULL,NULL,'Pierce Brown'),(138,'9780425285985','Light Bringer','2025-11-06 22:04:33','2023-07-25','Darrow faces the consequences of his choices in the latest Red Rising book.',NULL,NULL,'Pierce Brown'),(139,'9780399256752','Legend','2025-11-06 22:04:33','2011-11-29','A prodigy and a criminal uncover dark secrets in a dystopian Republic.',NULL,NULL,'Marie Lu'),(140,'9780399256783','Prodigy','2025-11-06 22:04:33','2013-01-29','June and Day join rebels fighting against the government.',NULL,NULL,'Marie Lu'),(141,'9780399256776','Champion','2025-11-06 22:04:33','2013-11-05','The final confrontation decides the future of the Republic.',NULL,NULL,'Marie Lu'),(142,'9781250221704','Rebel','2025-11-06 22:04:33','2019-10-01','A new generation faces corruption and rebellion in a futuristic world.',NULL,NULL,'Marie Lu');
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dchu`@`localhost`*/ /*!50003 TRIGGER prevent_direct_book_insert
BEFORE INSERT ON books FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into books, use addBook() or formToBook()';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `book_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `creation_date` timestamp NULL DEFAULT current_timestamp(),
  `comment_text` text NOT NULL,
  `depth` int(11) NOT NULL DEFAULT 0,
  `deletion_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (24,1,2,NULL,'2025-11-06 22:04:33','Great Book I loved the setting that this book was set in, 5/5 Stars for sure.',0,NULL),(24,2,5,NULL,'2025-11-06 22:04:33','Good, but could have done with less romance',0,NULL),(4,3,5,NULL,'2025-11-06 22:04:33','Loved the description of the damages that the strain caused on our technology, very interesting',0,NULL),(78,4,4,NULL,'2025-11-06 22:04:33','Puddle is pleased, but wishes for more interaction between the characters...',0,NULL),(140,5,3,NULL,'2025-11-06 22:04:33','REBEL!!!, I greatly enjoyed this book but I am left wondering about somethings the characters seemed to leave out.',0,NULL),(24,8,4,1,'2025-11-06 22:04:33','Puddle also enjoyed the world these characters live in, makes Puddle want to see more.',1,NULL),(140,9,5,5,'2025-11-06 22:04:33','I think that you probably just need to reread the book',1,NULL),(4,10,2,3,'2025-11-06 22:04:33','I also loved that, especially since the technology is limited to what might have been possible in the 1960s',1,NULL),(140,11,3,8,'2025-11-06 22:04:33','No way, They just don\'t have as much depth as I wanted, but it still made for a quite the book',2,NULL),(140,12,5,9,'2025-11-06 22:04:33','If that\'s how you feel then ok, just saying...',2,NULL),(60,13,3,NULL,'2025-11-06 22:04:33','Classic! I wish I could go to Hogwarts myself. 10/10 childhood nostalgia.',0,NULL),(60,14,17,NULL,'2025-11-06 22:04:33','This book started my love for fantasy! Still reread it every winter.',0,NULL),(52,15,15,NULL,'2025-11-06 22:04:33','1984 feels more real every year... terrifyingly good.',0,NULL),(51,16,23,NULL,'2025-11-06 22:04:33','Atticus Finch remains one of the best-written characters in literature.',0,NULL),(57,17,29,NULL,'2025-11-06 22:04:33','Mr. Darcy supremacy. That\'s it, that\'s the review.',0,NULL),(78,18,10,NULL,'2025-11-06 22:04:33','One of the most haunting books I\'ve read. The ending stuck with me for weeks.',0,NULL),(106,19,41,NULL,'2025-11-06 22:04:33','I was 13 when I first read Eragon, and it blew my mind. The nostalgia is strong.',0,NULL),(84,20,8,NULL,'2025-11-06 22:04:33','Katniss is such a strong character. Love the pacing and worldbuilding.',0,NULL),(62,21,19,NULL,'2025-11-06 22:04:33','So tragic yet so beautiful. Fitzgerald\'s writing is unmatched.',0,NULL),(114,22,21,NULL,'2025-11-06 22:04:33','One of the best mystery thrillers ever. Kept me guessing until the end!',0,NULL);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dchu`@`localhost`*/ /*!50003 TRIGGER set_comment_depth
BEFORE INSERT ON comments FOR EACH ROW BEGIN
    DECLARE D INT;
    IF NEW.parent_id IS NULL THEN
        SET NEW.depth = 0;
    ELSE
        SELECT depth INTO D FROM comments WHERE comment_id = NEW.parent_id;

        IF D IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid parent chain';
        ELSE
            SET NEW.depth = D + 1;
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dchu`@`localhost`*/ /*!50003 TRIGGER delete_comments
BEFORE DELETE ON comments FOR EACH ROW BEGIN
    DECLARE has_child INT DEFAULT 0;
    IF @skip_trig IS NULL THEN
        SELECT EXISTS (
            SELECT 1 FROM comments C WHERE C.parent_id = OLD.comment_id 
        ) INTO has_child;

        IF has_child THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
            'Cannot hard-delete a comment that has replies, use deleteComment()';
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `formgenres`
--

DROP TABLE IF EXISTS `formgenres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formgenres` (
  `form_id` int(11) NOT NULL,
  `genre` varchar(16) NOT NULL,
  PRIMARY KEY (`form_id`,`genre`),
  KEY `genre` (`genre`),
  CONSTRAINT `formgenres_ibfk_1` FOREIGN KEY (`genre`) REFERENCES `genres` (`genre`) ON DELETE CASCADE,
  CONSTRAINT `formgenres_ibfk_2` FOREIGN KEY (`form_id`) REFERENCES `forms` (`form_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formgenres`
--

LOCK TABLES `formgenres` WRITE;
/*!40000 ALTER TABLE `formgenres` DISABLE KEYS */;
INSERT INTO `formgenres` VALUES (1,'Historical'),(1,'Non-Fiction'),(2,'Adventure'),(2,'Fantasy'),(2,'Historical'),(2,'Sci-Fi'),(3,'Adventure'),(3,'Fantasy'),(3,'Historical'),(3,'Sci-Fi'),(4,'Adventure'),(4,'Fantasy'),(4,'Historical'),(4,'Sci-Fi'),(5,'Crime'),(5,'Mystery'),(5,'Thriller');
/*!40000 ALTER TABLE `formgenres` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dchu`@`localhost`*/ /*!50003 TRIGGER prevent_direct_formgenre_insert
BEFORE INSERT ON formgenres FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into formgenres, use addForm()';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forms` (
  `form_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `isbn` varchar(13) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_path` varchar(512) DEFAULT NULL,
  `published` date DEFAULT NULL,
  `author` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `creation_date` timestamp NULL DEFAULT current_timestamp(),
  `approve_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`form_id`),
  UNIQUE KEY `dedupe` (`user_id`,`isbn`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `forms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `forms_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`),
  CONSTRAINT `constrain_form_isbn` CHECK (`isbn` regexp '^[0-9]{13}$')
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
INSERT INTO `forms` VALUES (1,2,NULL,'9781451648539','Steve Jobs',NULL,'2011-10-01','Walter Isaacson','Walter Isaacson\'s worldwide bestselling biography of Apple cofounder Steve Jobs. Based on more than forty interviews with Steve Jobs conducted over two years--as well as interviews with more than 100 family members, friends, adversaries, competitors, and colleagues--Walter Isaacson has written a riveting story of the roller-coaster life and searingly intense personality of a creative entrepreneur whose passion for perfection and ferocious drive revolutionized six industries: personal computers, animated movies, music, phones, tablet computing, and digital publishing. Isaacson\'s portrait touched millions of readers. At a time when America is seeking ways to sustain its innovative edge, Jobs stands as the ultimate icon of inventiveness and applied imagination. He knew that the best way to create value in the twenty-first century was to connect creativity with technology. He built a company where leaps of the imagination were combined with remarkable feats of engineering. Although Jobs cooperated with the author, he asked for no control over what was written. He put nothing off-limits. He encouraged the people he knew to speak honestly. He himself spoke candidly about the people he worked with and competed against. His friends, foes, and colleagues offer an unvarnished view of the passions, perfectionism, obsessions, artistry, devilry, and compulsion for control that shaped his approach to business and the innovative products that resulted. His tale is instructive and cautionary, filled with lessons about innovation, character, leadership, and values. Steve Jobs is the inspiration for the movie of the same name starring Michael Fassbender, Kate Winslet, Seth Rogen, and Jeff Daniels, directed by Danny Boyle with a screenplay by Aaron Sorkin.','2025-11-06 22:04:33',NULL),(2,2,NULL,'9781416971733','Leviathan',NULL,'2009-10-06','Scott Westerfeld','Prince Aleksander, would-be heir to the Austro-Hungarian throne, is on the run. His own people have turned on him. His title is worthless. All he has is a battletorn war machine and a loyal crew of men.\n\nDeryn Sharp is a commoner, disguised as a boy in the British Air Service. She\'s a brilliant airman. But her secret is in constant danger of being discovered.\n\nWith World War I brewing, Alek and Deryn\'s paths cross in the most unexpected way…taking them on a fantastical, around-the-world adventure that will change both their lives forever.','2025-11-06 22:04:33',NULL),(3,2,NULL,'9781416971757','Behemoth',NULL,'2010-10-05','Scott Westerfeld','The behemoth is the fiercest creature in the British navy. It can swallow enemy battleships with one bite. The Darwinists will need it, now that they are at war with the Clanker powers.\n\nDeryn is a girl posing as a boy in the British Air Service, and Alek is the heir to an empire posing as a commoner. Finally together aboard the airship Leviathan, they hope to bring the war to a halt. But when disaster strikes the Leviathan\'s peacekeeping mission, they find themselves alone and hunted in enemy territory. ','2025-11-06 22:04:33',NULL),(4,2,NULL,'9781416971771','Goliath',NULL,'2011-01-24','Scott Westerfeld','Alek and Deryn are abroad the Leviathan when the ship is ordered to pick up an unusual passenger. This brilliant/maniacal inventor claims to have a weapon called Goliath that can end the war. But whose side is he really on?\n\nWhile on their top-secret mission, Alek finally discovers Deryn\'s deeply kept secret. Two, actually. Not only is Deryn a girl disguised as a guy...she has feelings for Alek.\n\nThe crown, true love with a commoner, and the destruction of a great city all hang on Alek\'s next--and final--move.\n\nThe thunderous conclusion to Scott Westerfeld\'s Leviathan series, which was called \"sure to become a classic\" (SLJ).','2025-11-06 22:04:33',NULL),(5,5,NULL,'9781478945185','Instinct',NULL,'2017-06-26','James Patterson','The life Dr. Dylan Reinhart saves may be his own\n\nDr. Dylan Reinhart wrote the book on criminal behavior. Literally--he\'s a renowned, bestselling Ivy League expert on the subject. When a copy of his book turns up at a gruesome murder scene--along with a threatening message from the killer--it looks like someone has been taking notes.\n\nElizabeth Needham is the headstrong and brilliant NYPD Detective in charge of the case who recruits Dylan to help investigate another souvenir left at the scene--a playing card. Another murder, another card--and now Dylan suspects that the cards aren\'t a signature, they\'re a deadly hint--pointing directly toward the next victim.\n\nAs tabloid headlines about the killer known as \"The Dealer\" scream from newstands, New York City descends into panic. With the cops at a loss, it\'s up to Dylan to hunt down a serial killer unlike any the city has ever seen. Only someone with Dylan\'s expertise can hope to go inside the mind of a criminal and convince The Dealer to lay down his cards. But after thinking like a criminal--could Dylan become one?','2025-11-06 22:04:33',NULL);
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dchu`@`localhost`*/ /*!50003 TRIGGER prevent_direct_form_insert
BEFORE INSERT ON forms FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into forms, use addForm()';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genres` (
  `genre` varchar(16) NOT NULL,
  PRIMARY KEY (`genre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genres`
--

LOCK TABLES `genres` WRITE;
/*!40000 ALTER TABLE `genres` DISABLE KEYS */;
INSERT INTO `genres` VALUES ('Action'),('Adventure'),('Classic'),('Crime'),('Dystopian'),('Fantasy'),('Historical'),('Horror'),('Mystery'),('Non-Fiction'),('Romance'),('Sci-Fi'),('Thriller');
/*!40000 ALTER TABLE `genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `randbookgenres`
--

DROP TABLE IF EXISTS `randbookgenres`;
/*!50001 DROP VIEW IF EXISTS `randbookgenres`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `randbookgenres` AS SELECT
 1 AS `genre`,
  1 AS `avg_rating` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `randbooks`
--

DROP TABLE IF EXISTS `randbooks`;
/*!50001 DROP VIEW IF EXISTS `randbooks`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `randbooks` AS SELECT
 1 AS `book_id`,
  1 AS `title`,
  1 AS `author`,
  1 AS `image_path` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_date` timestamp NULL DEFAULT current_timestamp(),
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`rating_id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `constrain_rating` CHECK (`rating` between 0 and 5)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (1,24,2,'2025-11-06 22:04:33',5),(2,24,5,'2025-11-06 22:04:33',3),(3,4,5,'2025-11-06 22:04:33',5),(4,78,4,'2025-11-06 22:04:33',4),(5,78,3,'2025-11-06 22:04:33',1),(6,140,3,'2025-11-06 22:04:33',5);
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shadowcomments`
--

DROP TABLE IF EXISTS `shadowcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shadowcomments` (
  `book_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `creation_date` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `depth` int(11) NOT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deletion_date` timestamp NULL DEFAULT current_timestamp(),
  `reason` enum('USER','ADMIN','NONE') DEFAULT NULL,
  `action` enum('HARD','SOFT') DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `book_id` (`book_id`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `shadowcomments_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  CONSTRAINT `shadowcomments_ibfk_2` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shadowcomments`
--

LOCK TABLES `shadowcomments` WRITE;
/*!40000 ALTER TABLE `shadowcomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `shadowcomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `topthreebooks`
--

DROP TABLE IF EXISTS `topthreebooks`;
/*!50001 DROP VIEW IF EXISTS `topthreebooks`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `topthreebooks` AS SELECT
 1 AS `book_id`,
  1 AS `title`,
  1 AS `author`,
  1 AS `avg_rating`,
  1 AS `totalratings` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `toptwentybooks`
--

DROP TABLE IF EXISTS `toptwentybooks`;
/*!50001 DROP VIEW IF EXISTS `toptwentybooks`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `toptwentybooks` AS SELECT
 1 AS `book_id`,
  1 AS `title`,
  1 AS `author`,
  1 AS `summary`,
  1 AS `avg_rating`,
  1 AS `totalratings` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(512) DEFAULT NULL,
  `creation_date` timestamp NULL DEFAULT current_timestamp(),
  `deletion_date` timestamp NULL DEFAULT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci `ENCRYPTION_KEY_ID`=100;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'2025-11-06 22:02:00',NULL,'Test','$2y$10$jzsdFb/NVamhUzXHKu/VhuLU3mWSkLJC1b6gRh0qmBr38t4UmwCE2',1),(2,NULL,'2025-11-06 22:04:33',NULL,'ChuFam','$2y$10$G4vkc6sjFB0QmAKWISRtZOh7RoF0k95NjpHu8vEhIsG92mZnaI/Wa',1),(3,NULL,'2025-11-06 22:04:33',NULL,'DangerMANN','$2y$10$HAvcc8eIO52Q9oZglyU.N.Z.7.e/nFQt66d3lCvuex1MvFGLFPdb.',1),(4,NULL,'2025-11-06 22:04:33',NULL,'SillyPuddle','$2y$10$2Pmg87ImZimWa5Kaq3wmGuKEI.snx/xdZWU36I2ZWIjNrPXU/T2sS',1),(5,NULL,'2025-11-06 22:04:33',NULL,'Jebediah_KSP','$2y$10$rWlq8ldqQv7KRtnUXNCUZeK.uh1dDF8IqfD.sQ96BY1x6txVl4ZOi',1),(6,NULL,'2025-11-06 22:04:33',NULL,'acorona','$2y$12$7xQv3s1c4uKqP8p3XyZ8YuH0W3Y6mJk0x9b1v2Lr8Qf5E1d7KcH2a',1),(7,NULL,'2025-11-06 22:04:33',NULL,'pixelpapyrus','$2y$12$Z8Lr1cQn6TgQe3Vb5Kp9BuWw2r7YhNn8Qd4Ue6Zt1Lk2Jp5Qw9EKa',1),(8,NULL,'2025-11-06 22:04:33',NULL,'quillquake','$2y$12$H1Qp7Zs2Lk8Bn4Tj5Cw9UrYi6Mf3Na4Rz7Ux1We2Qp6Lt8Vb3Jd2',1),(9,NULL,'2025-11-06 22:04:33',NULL,'obeltranbelt','$2y$12$86LhqhABfsELKfojUX5/..z59bDyZ2KO5PPK/BCLRnRKkPSU7/yWS',1),(10,NULL,'2025-11-06 22:04:33',NULL,'Hero','$2y$12$eTUDqXgkgbssSpcCPkKAJuoW4.8CjZlmZMtENW5CHRWZM4R1Yo3Ey',1),(11,NULL,'2025-11-06 22:04:33',NULL,'DaBookGuru','$2y$12$HT9yPRtrdieS3wBZNZL2Xuc2paclqgvjhO4YRx5fj854ULMfeF0I2',1),(12,NULL,'2025-11-06 22:04:33',NULL,'ABCDE','$2y$12$cuBq40OW/GCP2jYwEFMDl.rODyUCokvLiU9USJ..BaJWJ3Ctmq0Cm',1),(13,NULL,'2025-11-06 22:04:33',NULL,'jvillalobos','$2y$12$bxkUliRrPJ2pcj/Vdgkb6uIbDpvzc2P6TeoP8D9LU/6WH.xhJPJIG',1),(14,NULL,'2025-11-06 22:04:33',NULL,'readah','$2y$12$SXC.FHnPBVE1jrNtFD7A1OCKPLowApeBz7Fp3Uqy86yoxHO4cNb4u',1),(15,NULL,'2025-11-06 22:04:33',NULL,'bookworm','$2y$12$ZezQCSTaW/iRFGmDGrDoVetlpRTmwlZcjfA4YC./yfbGQ.eWaGcim',1),(16,NULL,'2025-11-06 22:04:33',NULL,'literati','$2y$12$04ZhiYs5ilWt4qEDt6pzSOjzFgtVwuI4rMuLXTyS1WZdFV7IwVYPK',1),(17,NULL,'2025-11-06 22:04:33',NULL,'mangafan','$2y$12$PVNdy/SncarymSN9s1zuQOt5ovqS1oHBwGEjNZHOQ4CVK5qLiHw.G',1),(18,NULL,'2025-11-06 22:04:33',NULL,'pageflipper','$2y$12$5Fp.S15V0hNgTbzyRxQZI.obVydKDXJRAEh8KBJSbggArF.DVagby',1),(19,NULL,'2025-11-06 22:04:33',NULL,'inklover','$2y$12$HACx.VDbPuETgRBKDkmMieE50HjnGcz1wmq1TJFiJQogKXqkS4jL.',1),(20,NULL,'2025-11-06 22:04:33',NULL,'novelnerd','$2y$12$5FlhNB237h/6/O2dfR9imO.h5JljBWRFpikSbDn3STFkcQhzmsvg.',1),(21,NULL,'2025-11-06 22:04:33',NULL,'storyseeker','$2y$12$zJEkNzfupFxiTp70zhKQju9fbJpXBCYMtjMreUPj.ISbUVmbnwl2G',1),(22,NULL,'2025-11-06 22:04:33',NULL,'wordsmith','$2y$12$HOfh8wDs.Y86ICNeZl.Nieyq7USV5QgETYr1zaaiKu0w9yIuo7S2K',1),(23,NULL,'2025-11-06 22:04:33',NULL,'fictionaddict','$2y$12$biEQeMjNfQYBOd5CreWpGeJm1VSyYHd7xMi00f4OfosPEUITUP56.',1),(24,NULL,'2025-11-06 22:04:33',NULL,'classiclvr','$2y$12$sbA.lltWCzFfq7nn18vbAeCImoUn7.4aPHM4DL2eiQVcTPTI.Kyi6',1),(25,NULL,'2025-11-06 22:04:33',NULL,'fantasyfiend','$2y$12$tFgK5oRPHh9z1Inrf1UPd.DTUfIbfMjSXnuU6iB3Cjb9aaSA5Cg7u',1),(26,NULL,'2025-11-06 22:04:33',NULL,'sciquest','$2y$12$HjFv66ZjP0/46bkAIcQAIe0GQ4CboFXqR8Kb.Tigc0fZkNRC7bDCS',1),(27,NULL,'2025-11-06 22:04:33',NULL,'mysterybuff','$2y$12$HZtuyAJZ2frRQNSkqnwnyeSMt6OQc0SEmCasaQu3BtJ2Vy0Irrw/e',1),(28,NULL,'2025-11-06 22:04:33',NULL,'horrorhound','$2y$12$DeJ247qK3W.9/zXme3m9t.sol81iwVapbnC1JyDkjjKIY263AUJ/K',1),(29,NULL,'2025-11-06 22:04:33',NULL,'romancereader','$2y$12$WtQ2yCzJm.1Gv0gptYilEuk4SkFLUCp.EUBOhpC3rWpHGeYWTlIVa',1),(30,NULL,'2025-11-06 22:04:33',NULL,'thrillerthrill','$2y$12$sepQB/qXgAf4JXS5wd.I/OgR7PYfmpBMw8dxGsqp8xQcdlO73FiN2',1),(31,NULL,'2025-11-06 22:04:33',NULL,'historyhunter','$2y$12$cK8LbbbKMv6Txb.3xr7X9OFeraN0OURiHFGizkm0u9q1nUZ8qBSp2',1),(32,NULL,'2025-11-06 22:04:33',NULL,'austenfan','$2y$12$QncU1qH71BQ4QwqByBpyauhpiqGxweJWORz8wmKpa1gQPNBaNK8g6',1),(33,NULL,'2025-11-06 22:04:33',NULL,'camusvibes','$2y$12$esP01r10vQx3amaJRjcLGeVUfszHTUIg87gTQFXqpKEBjMgYKImzO',1),(34,NULL,'2025-11-06 22:04:33',NULL,'bookhoarder','$2y$12$6GacBarkSXysLkDFvo8m.u0Nc68vCsBYPlwC62pa9.FbUjw2oAZOK',1),(35,NULL,'2025-11-06 22:04:33',NULL,'dunelord','$2y$12$AK4w08Kg3dhlo86qAqKrkOP4.hcv/p7d8o2Si7qB.uKudQeN4Ksqq',1),(36,NULL,'2025-11-06 22:04:33',NULL,'magicreader','$2y$12$YKMCD6iVCstiyHCoCOAaxeLuQGo.5NmyG7Zt/mjx4A3Pkt5QiVxEK',1),(37,NULL,'2025-11-06 22:04:33',NULL,'prosepilot','$2y$12$S2aSw0Gnl6iY8rMxX7Urn.q.IGe4RESHV9vjJnD1ZxbIfq29fGqQC',1),(38,NULL,'2025-11-06 22:04:33',NULL,'plotplunge','$2y$12$z2XodZIZA4T4D0izmgoiduuHIvEvpTXDoA8zuQMbzBxCYdFLrWOze',1),(39,NULL,'2025-11-06 22:04:33',NULL,'covercollector','$2y$12$cI5XKIZ/emG0Yo.6ivoTueRmQdM95nddYbSGQwQQYAzeBOkaH5Wp6',1),(40,NULL,'2025-11-06 22:04:33',NULL,'librarianmode','$2y$12$mLegBNGXjS4dlc1Hqm.57ObeZbNmLX9P5g6/cfa30Rw8HIwilghgS',1),(41,NULL,'2025-11-06 22:04:33',NULL,'papertrail','$2y$12$GGK9ElS4KpDNGxofddfR/eV18FVCRvk6fmZo6e6ZOglWQrn9QiOyy',1),(42,NULL,'2025-11-06 22:04:33',NULL,'curiousreader','$2y$12$g24E9vSw9hLEvuDRH7vNPu4.j1GDykLCCZ5Jm3cAvKERA5oPF4ZnS',1),(43,NULL,'2025-11-06 22:04:33',NULL,'bookmarkit','$2y$12$xBB5tEi/tnDQBtTC4wloa.RFC6jNQFvk1HHCSMHnd9pbM5J3nTShu',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`dchu`@`localhost`*/ /*!50003 TRIGGER toggle_users_active
BEFORE UPDATE ON users FOR EACH ROW BEGIN
    IF OLD.is_active = 1 AND NEW.is_active = 0 THEN
        SET NEW.deletion_date = CURRENT_TIMESTAMP;
    ELSEIF OLD.is_active = 0 AND NEW.is_active = 1 THEN
        SET NEW.deletion_date = NULL;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `randbookgenres`
--

/*!50001 DROP VIEW IF EXISTS `randbookgenres`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dchu`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `randbookgenres` AS select `g`.`genre` AS `genre`,round(avg(`r`.`rating`),2) AS `avg_rating` from ((`ratings` `r` join `bookgenres` `bg` on(`bg`.`book_id` = `r`.`book_id`)) join `genres` `g` on(`g`.`genre` = `bg`.`genre`)) group by `g`.`genre` order by round(avg(`r`.`rating`),2) desc,`g`.`genre` limit 5 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `randbooks`
--

/*!50001 DROP VIEW IF EXISTS `randbooks`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dchu`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `randbooks` AS select `b`.`book_id` AS `book_id`,`b`.`title` AS `title`,`b`.`author` AS `author`,`b`.`image_path` AS `image_path` from `books` `b` order by rand() limit 10 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `topthreebooks`
--

/*!50001 DROP VIEW IF EXISTS `topthreebooks`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dchu`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `topthreebooks` AS select `b`.`book_id` AS `book_id`,`b`.`title` AS `title`,`b`.`author` AS `author`,avg(`r`.`rating`) AS `avg_rating`,count(`r`.`rating_id`) AS `totalratings` from (`books` `b` join `ratings` `r` on(`b`.`book_id` = `r`.`book_id`)) group by `b`.`book_id` order by count(`r`.`rating_id`) desc,avg(`r`.`rating`) desc limit 3 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `toptwentybooks`
--

/*!50001 DROP VIEW IF EXISTS `toptwentybooks`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dchu`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `toptwentybooks` AS select `b`.`book_id` AS `book_id`,`b`.`title` AS `title`,`b`.`author` AS `author`,`b`.`summary` AS `summary`,avg(`r`.`rating`) AS `avg_rating`,count(`r`.`rating_id`) AS `totalratings` from (`books` `b` join `ratings` `r` on(`b`.`book_id` = `r`.`book_id`)) group by `b`.`book_id` order by count(`r`.`rating_id`) desc,avg(`r`.`rating`) desc limit 20 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-06 14:48:07
