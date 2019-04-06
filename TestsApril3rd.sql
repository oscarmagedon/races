Tests on apr 3rd:

Evangeline (14)  downs has retires on 


Number :: Horse :: RaceID :: Stats

2nd => Horse 6 =>       => This is already saved
5th => Horse 9 => 39264 => this is already retired


SAVING RESULTS


Will Rogers 

4th => 39901


/*
Tickets on 5th evangeline
*/

SELECT * FROM `tickets` WHERE race_id in(39264,39216) 


/*
All evangeline on MASTER
*/

SELECT * FROM `races` 
WHERE race_date = CURRENT_DATE 
and hipodrome_id = 14 and center_id = 1


/**
	Test ONE :: set 5th on EVA to get advantage
	of the retired already, creating the arrays

*/



/**
	Test TWO :: Set retired horses from BOVADA

*/


/**
	Test THREE :: Retire bovada just before 
	save and calculate.

*/

