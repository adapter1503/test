SELECT
    lastname,
    firstname,
    mother_id,
    age
FROM
    test.person
WHERE
    (
            mother_id IS null
            AND age < (
            SELECT
                MAX(age)
            FROM
                test.person
        )
        )
LIMIT
    1;
