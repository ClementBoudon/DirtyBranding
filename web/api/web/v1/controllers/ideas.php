<?php
    $ideas = $app['controllers_factory'];

    /*
    GET /ideas/{idea}/brands?suffixes[]=&prefixes[]=
    A partir d'une idée et optionnellement de prefixes / suffixes,
    nous retournons une liste de marque possible :
    - l'idée de base
    - + toutes les possibilités d'associations préfixes / suffixes
    Exemples :
        Idee 1 : Idee 1, Idee 1 Suf1, Pref1 Idee 1, Pref1 Idee 1 Suf 1, etc.
    */
    $ideas->get('{idea}/brands',
        function (Silex\Application $app, $idea) {

            $idea_escaped = $app->escape(urldecode($idea));

            $array_brands = array($idea_escaped);
            $array_prefixes = array();
            $array_suffixes = array();

            $default_separator = ' ';

            // Traitement des paramètres
            if(null !== $app['request']->get('prefixes'))
            {
                foreach($app['request']->get('prefixes') as $prefixes_src)
                {
                    $array_prefixes[]=$app->escape($prefixes_src);
                }
            }

            if(null !== $app['request']->get('suffixes'))
            {
                foreach($app['request']->get('suffixes') as $suffixes_src)
                {
                    $array_suffixes[]=$app->escape($suffixes_src);
                }
            }

            //Génération des associations
            foreach($array_prefixes as $prefix)
            {
                //Seulement préfixe
                $array_brands[] = $prefix
                .$default_separator
                .$idea_escaped;

                //Préfixe ET suffixe
                foreach($array_suffixes as $suffix)
                {
                    $array_brands[] = $prefix
                    .$default_separator
                    .$idea_escaped
                    .$default_separator
                    .$suffix;
                }
            }

            //Seulement suffixe
            foreach($array_suffixes as $suffix)
            {
                $array_brands[] = $idea_escaped
                .$default_separator
                .$suffix;
            }

            //Génération de toutes les possibilités
            //TODO


            //Log de la recherche
            $details = 'Return : '.json_encode($array_brands)
            .' | Prefixes : '.serialize($array_prefixes)
            .' | Suffixes : '.serialize($array_suffixes)
            .' | SERVER : '.serialize($_SERVER)
            .' | COOKIE : '.serialize($_COOKIE)
            .' | GET : '.serialize($_GET)
            .' | POST : '.serialize($_POST);
            $sql_ins = "INSERT
            INTO SearchIdeaLog
            (search, dt_search, details)
            VALUES
            (?,?,?)";
            $stmt_ins = $app['db']->prepare($sql_ins);
            $stmt_ins->bindValue(1, $idea_escaped);
            $stmt_ins->bindValue(2, date('Y-m-d H:i:s'));
            $stmt_ins->bindValue(3, $app->escape($details));
            $stmt_ins->execute();

            return json_encode($array_brands);
    });

    return $ideas;
