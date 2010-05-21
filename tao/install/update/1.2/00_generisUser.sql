UPDATE `statements` SET `object` = 'http://www.tao.lu/middleware/taoqual.rdf#Role' WHERE `object` = 'http://www.tao.lu/middleware/taoqual.rdf#i118588820437156';
UPDATE `statements` SET `subject` = 'http://www.tao.lu/middleware/taoqual.rdf#Role' WHERE `subject` = 'http://www.tao.lu/middleware/taoqual.rdf#i118588820437156' ;

DELETE FROM `statements` WHERE `subject` = 'http://www.tao.lu/middleware/taoqual.rdf#i1207061000021883700';
DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/middleware/Interview.rdf#i121863605145680';
DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/middleware/Interview.rdf#i121939806049066';

DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/middleware/Interview.rdf#i122786668726350';
DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/middleware/Interview.rdf#i122786657224088';
DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/middleware/Interview.rdf#i122786664635240';
DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/middleware/Interview.rdf#fteHeight';

DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/middleware/taoqual.rdf#i1190121738750';


INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#User', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#User', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Class that will gather all Generis'' Users ', 'EN','generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#User', 'http://www.w3.org/2000/01/rdf-schema#label', 'User', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#User', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN',  'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#User', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource', 'EN',  'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#User', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN',  'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Tao Manager', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', 'http://www.w3.org/2000/01/rdf-schema#label', 'TaoManager', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/generis.rdf#User', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUserRole', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Workflow User: default role assigned to every backend user, not deletable', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUserRole', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUserRole', 'http://www.w3.org/2000/01/rdf-schema#label', 'WfUser', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUserRole', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUserRole', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUserRole', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUserRole', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/generis.rdf#User', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Tao Subject Role', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', 'http://www.w3.org/2000/01/rdf-schema#label', 'TaoSubjectRole', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOffice', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(12, 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/generis.rdf#User', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');


INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/2000/01/rdf-schema#comment', 'User Login', 'EN',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/2000/01/rdf-schema#label', 'login', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/generis.rdf#User', '','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#login', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');


INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/2000/01/rdf-schema#comment', 'User Password ', 'EN',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/2000/01/rdf-schema#label', 'password', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/generis.rdf#User', '','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#password', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/2000/01/rdf-schema#comment', 'User Last Name ', 'EN',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/2000/01/rdf-schema#label', 'LastName', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/generis.rdf#User', '','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userLastName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/2000/01/rdf-schema#comment', 'User First Name ', 'EN',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/2000/01/rdf-schema#label', 'FirstName', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/generis.rdf#User', '','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/2000/01/rdf-schema#comment', 'User Mail  ', 'EN',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/2000/01/rdf-schema#label', 'Mail', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/generis.rdf#User', '','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userMail', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAO.rdf#Languages', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/2000/01/rdf-schema#comment', 'User Default Language  ', 'EN',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/2000/01/rdf-schema#label', 'Default Language', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/generis.rdf#User', '','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAO.rdf#Languages', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/2000/01/rdf-schema#comment', 'User Interface Language  ', 'EN',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/2000/01/rdf-schema#label', 'UILanguage', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/generis.rdf#User', '','tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(7, 'http://www.tao.lu/Ontologies/generis.rdf#userUILg', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '',  'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');


INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOffice', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOffice', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOffice', 'http://www.w3.org/2000/01/rdf-schema#label', 'FrontOffice', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOffice', 'http://www.w3.org/2000/01/rdf-schema#comment', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOffice', 'http://www.tao.lu/middleware/Interview.rdf#fteHeight', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOffice', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/middleware/taoqual.rdf#Role', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', 'http://www.w3.org/2000/01/rdf-schema#label', 'BackOffice', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', 'http://www.w3.org/2000/01/rdf-schema#comment', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', 'http://www.tao.lu/middleware/Interview.rdf#fteHeight', '', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(6, 'http://www.tao.lu/Ontologies/TAO.rdf#BackOffice', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/middleware/taoqual.rdf#Role', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');


INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES
(8, 'http://localhost/middleware/tao.rdf#installator', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole', '', '', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(8, 'http://localhost/middleware/tao.rdf#installator', 'http://www.w3.org/2000/01/rdf-schema#label', 'installator', '', '', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(8, 'http://localhost/middleware/tao.rdf#installator', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Generated during update from user table on2010-05-17T18:54:33+0200', '', '', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(8, 'http://localhost/middleware/tao.rdf#installator', 'http://www.tao.lu/Ontologies/generis.rdf#login', 'generis', '', '', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(8, 'http://localhost/middleware/tao.rdf#installator', 'http://www.tao.lu/Ontologies/generis.rdf#password', 'b01a52f727b0810639526fe2c8188331', '', '', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');
