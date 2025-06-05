<?php

namespace Ilyas;

use Ilyas\Repository\Repository;

/**
 * Class VisitorsCounter
 * @package Ilyas
 */
class VisitorsCounter
{
    /**
     * @param Repository $repository
     * @param int $second
     * @return mixed
     */
    public static function getCount(Repository $repository, $second = 300)
    {
        //First we have to start the session only if it has not been started
        if(!isset($_SESSION)){
            session_start();
        }
        $sessionId = session_id();   	//We assign the session id to the variable $session_id
        $time = time(); 	            //We assign the current time to the variable $time
        $timeLimit = $time - $second;	//We give the session only n-minutes if it exists

        // We need to check the session_id is already stored or not
        $num = $repository->getVisitorBySessionID($sessionId);
        //if it doesn't exist, then we'll store it And if does exist, we will update the session's time in the DB
        if(!$num){
            $repository->addNewVisitor($sessionId, $time);
        }else{
            $repository->updateVisitor($sessionId, $time);
        }

        //Now the following code is to get the count of visitors currently online
        $count = $repository->getAllVisitors();

        //Now, check if the session was stored for more than n-minutes and delete it if it is.
        $repository->deleteOfflineVisitors($timeLimit);

        // close connect to DB
        $repository->close();

        return empty($count) ? 0 : count($count);
    }
}