<?
/**
* @param boolean $filled
* @param string $site
* @param string $companyPart
* @param string $sort примимает параметры 'name', 'date', 'filling', default.
* От этого зависит что передастся в $sortClause
* @param int $offset - смещение относительно начала получаемого списка
* @param int $limit - число строк в выборке
* если $site равен статическому константному значению, то в $siteClause запишется 's.name is null',
* если нет, то "s.name='$site'
* если длина строки $companyPart == 0, то $companyPartClause присвоится '1',
* если нет, то "co.name_ru like '$companyPart'"
* в переменную $sortClause записываются параметры запроса
* в зависимости от значения $filled == 'true' || else передаются разные запросы для подготовки и отправки запроса
* @return array содержащий все строки результирующего набора
*/
public function getAllCompaniesPageData($filled, $site, $companyPart, $sort, $offset, $limit) {
        $siteClause = $site==self::NO_SITE ? 's.name is null' : "s.name='$site'";
        $companyPartClause = strlen($companyPart)==0 ? '1' : "co.name_ru like '$companyPart'";
        $filledSum = 'cf.name_ru+cf.country_id+cf.region_id+cf.company_inn+cf.product+cf.category+cf.type+cf.presentation+cf.address_ru+cf.phone+cf.email';
        $sortClause = 'order by ';
        switch ($sort){
            case 'name':
                $sortClause .= 'company asc';
                break;
            case 'date':
                $sortClause .= 'dateadd desc';
                break;
            case 'filling':
                $sortClause .= $filledSum.' desc';
                break;
            default:
                $sortClause .= '1';
        }
        if ($filled == 'true') {
            $filledClause = 'cf.name_ru+cf.country_id+cf.region_id+cf.company_inn+cf.product+cf.category+cf.type+cf.presentation+cf.address_ru+cf.phone+cf.email=11';
            $sql = <<<SQL
select co.id as company_id, co.name_ru as company, ch1.result, date(ch1.dateadd) as dateadd
from catalogue.company co
left join (select company_id, max(id) as id from catalogue.`check` group by company_id) ch on ch.company_id=co.id
left join catalogue.`check` ch1 on ch1.id=ch.id
left join catalogue.company_filling cf on cf.company_id=co.id
left join ( select company_id, category_id
from catalogue.company_index ci
    /*where ci.is_checked=1*/ group by company_id, category_id) cc on cc.company_id=co.id
left join catalogue.sites s on s.category_id=cc.category_id
where $filledClause and $siteClause and $companyPartClause
$sortClause
limit $offset, $limit;
SQL;
            ($stmt = $this->db->prepare($sql))->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $filledClause = $filledSum.'<11';
            $sql = <<<SQL
select co.id as company_id, co.name_ru as company, ch1.result, date(ch1.dateadd) as dateadd
	, cf.name_ru, cf.country_id, cf.region_id, cf.company_inn, cf.product, cf.category, cf.type, cf.presentation, cf.address_ru, cf.phone, cf.email
from catalogue.company co
left join (select company_id, max(id) as id from catalogue.`check` group by company_id) ch on ch.company_id=co.id
left join catalogue.`check` ch1 on ch1.id=ch.id
left join catalogue.company_filling cf on cf.company_id=co.id
left join ( select company_id, category_id
from catalogue.company_index ci
    /*where ci.is_checked=1*/ group by company_id, category_id) cc on cc.company_id=co.id
left join catalogue.sites s on s.category_id=cc.category_id
where $filledClause and $siteClause and $companyPartClause
$sortClause
limit $offset, $limit;
SQL;
            ($stmt = $this->db->prepare($sql))->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }


Разобрать как работает метод.

Ответить на вопросы.
Где возможно, высказать свои предположения о названиях таблиц и полей, типе данных, обязательности и допустимых значениях полей, индексах:

1) Какие таблицы используются в запросах. Описать их структуры в форме оператора CREATE STATEMENT

2) Описать входные параметры метода (назначение, тип, допустимые значения)

3) Описать возможный результат метода (тип, структуру, количественные характеристики)

4) Написать PHP-код, формирующий HTML-таблицу с результатами метода. Булевские данные отображать как " " или "✓"

