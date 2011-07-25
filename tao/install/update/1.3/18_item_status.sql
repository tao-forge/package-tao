INSERT INTO "statements" ("modelID", "subject", "predicate", "object", "l_language", "author", "stread", "stedit", "stdelete") VALUES
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', 'http://www.w3.org/2000/01/rdf-schema#comment', '', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', 'http://www.w3.org/2000/01/rdf-schema#label', 'Model Status', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/generis.rd#generis_Ressource', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable', 'http://www.w3.org/2000/01/rdf-schema#comment', 'this item  model can be used in production', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable', 'http://www.w3.org/2000/01/rdf-schema#label', '', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDeprecated', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type','http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDeprecated','http://www.w3.org/2000/01/rdf-schema#comment', 'this item model is not supported anymore, we advise to migrate your items', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDeprecated','http://www.w3.org/2000/01/rdf-schema#label', 'Deprecated', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDevelopment', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type','http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDevelopment','http://www.w3.org/2000/01/rdf-schema#comment', 'this item model has not been tested in a production environment, we advise to be carefull with it', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDevelopment','http://www.w3.org/2000/01/rdf-schema#label', 'Development', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusExperimental', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type','http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusExperimental','http://www.w3.org/2000/01/rdf-schema#comment', 'this item model is still in conception, but you can try it, give your feedback and it will be improved.', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusExperimental','http://www.w3.org/2000/01/rdf-schema#label', 'Experimental', 'EN ', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The status of an item model', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.w3.org/2000/01/rdf-schema#label', 'Status', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#XHTML', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#i125933161031263', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusExperimental', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#QCM', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDeprecated', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#kohs', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#Ctest', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(10, 'http://www.tao.lu/Ontologies/TAOItem.rdf#campus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus', 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDeprecated', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');
