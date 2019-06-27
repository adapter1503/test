UPDATE person as p1
INNER JOIN (SELECT age FROM person ORDER BY age DESC LIMIT 1) as p2
INNER JOIN (SELECT id FROM person WHERE age < (SELECT MAX(age) FROM person LIMIT 1)) as p3
SET p1.age = p2.age
WHERE p1.age != p2.age AND p1.id = p3.id;
