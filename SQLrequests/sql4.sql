SELECT
    lastname,
    firstname,
    age
FROM
    person
WHERE
    (
            age =
            (
                SELECT
                    age
                FROM
                    person
                ORDER BY
                    age DESC LIMIT 1
            )
        )
;