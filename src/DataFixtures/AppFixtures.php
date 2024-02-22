<?php

namespace App\DataFixtures;

use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\{Genre, Movie, Review};
use App\Entity\{Quality, Theater, Room, Seat, Issue, Equipment};
use App\Entity\{MovieSession, Ticket, OrderTickets};

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $visitors = [];  // used later for creating movie reviews
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $account = 'visitor' . $i . '@example.org';
            $user->setEmail($account);
            $password = $this->hasher->hashPassword($user, $account);
            $user->setPassword($password);
            $user->setLastname($faker->lastName());
            $user->setFirstname($faker->firstName());
            $manager->persist($user);
            array_push($visitors, $user);
        }

        // Create reference users (used for demo purposes)
        $admin = new User();
        $admin->setEmail('admin@example.org');
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setLastname($faker->lastName());
        $admin->setFirstname($faker->firstName());
        $manager->persist($admin);

        $employee = new User();
        $employee->setEmail('employee@example.org');
        $employee->setPassword($this->hasher->hashPassword($employee, 'employee'));
        $employee->setRoles(['ROLE_EMPLOYEE']);
        $employee->setLastname($faker->lastName());
        $employee->setFirstname($faker->firstName());
        $manager->persist($employee);

        $visitor = new User();
        $visitor->setEmail('visitor@example.org');
        $visitor->setPassword($this->hasher->hashPassword($visitor, 'visitor'));
        $visitor->setLastname($faker->lastName());
        $visitor->setFirstname($faker->firstName());
        $manager->persist($visitor);
        array_push($visitors, $visitor);

        //We use associative arrays with the key as the name of the object and the value as the object
        //to use it as a reference in the next fixtures, e.g. $genres['Drame'] will return the Genre object with the name 'Drame'

        //Add the movie qualities
        $qualities = [
            '4K' => new Quality(),
            '3D' => new Quality(),
            '8K' => new Quality(),
            'IMAX' => new Quality(),
            '35mm' => new Quality()
        ];
        foreach ($qualities as $key => &$quality) {
            $quality->setName($key);
            $quality->setPrice($faker->numberBetween(8, 20));
            $manager->persist($quality);
        }

        //Add the movie genres
        $genres = [
            'Action' => new Genre(),
            'Aventure' => new Genre(),
            'Biographie' => new Genre(),
            'Comédie' => new Genre(),
            'Crime' => new Genre(),
            'Drame' => new Genre(),
            'Fantasy' => new Genre(),
            'Horreur' => new Genre(),
            'Mystère' => new Genre(),
            'Romance' => new Genre(),
            'Science-fiction' => new Genre(),
            'Thriller' => new Genre(),
            'Crime' => new Genre(),
            'Documentaire' => new Genre(),
            'Famille' => new Genre(),
            'Guerre' => new Genre(),
            'Histoire' => new Genre(),
            'Musical' => new Genre(),
            'Biographie' => new Genre(),
            'Sport' => new Genre(),
            'Western' => new Genre(),
            'Animation' => new Genre(),
            'Film noir' => new Genre(),
            'Musique' => new Genre()
        ];
        foreach ($genres as $key => &$genre) {
            $genre->setName($key);
            $manager->persist($genre);
        }

        //Add the movies (source: https://gist.github.com/stungeye/a3af50385215b758637e73eaacac93a3)
        $movies = array(
            'tt0111161' => ['The Shawshank Redemption', 1994, 142, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0068646' => ['The Godfather', 1972, 175, [$genres['Crime'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0071562' => ['The Godfather: Part II', 1974, 202, [$genres['Crime'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0468569' => ['The Dark Knight', 2008, 152, [$genres['Action'], $genres['Crime'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0050083' => ['12 Angry Men', 1957, 96, [$genres['Crime'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0108052' => ['Schindler\'s List', 1993, 195, [$genres['Biographie'], $genres['Drame'], $genres['Histoire']], 'description', 13, false, new Movie()],
            'tt0110912' => ['Pulp Fiction', 1994, 154, [$genres['Crime'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0167260' => ['The Lord of the Rings: The Return of the King', 2003, 201, [$genres['Action'], $genres['Aventure'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0060196' => ['Il buono, il brutto, il cattivo', 1966, 161, [$genres['Western']], 'description', 13, false, new Movie()],
            'tt0109830' => ['Forrest Gump', 1994, 142, [$genres['Drame'], $genres['Romance']], 'description', 13, false, new Movie()],
            'tt0120737' => ['The Lord of the Rings: The Fellowship of the Ring', 2001, 178, [$genres['Action'], $genres['Aventure'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0137523' => ['Fight Club', 1999, 139, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt1375666' => ['Inception', 2010, 148, [$genres['Action'], $genres['Aventure'], $genres['Science-fiction']], 'description', 13, false, new Movie()],
            'tt5813916' => ['Dag II', 2016, 135, [$genres['Action'], $genres['Drame'], $genres['Guerre']], 'description', 13, false, new Movie()],
            'tt8110330' => ['Dil Bechara', 2020, 101, [$genres['Comédie'], $genres['Drame'], $genres['Romance']], 'description', 13, false, new Movie()],
            'tt0073486' => ['One Flew Over the Cuckoo\'s Nest', 1975, 133, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0080684' => ['Star Wars: Episode V - The Empire Strikes Back', 1980, 124, [$genres['Action'], $genres['Aventure'], $genres['Fantasy']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Goodfellas', 1990, 146, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Matrix', 1999, 136, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Lord of the Rings: The Two Towers', 2002, 179, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['It\'s a Wonderful Life', 1946, 130, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Shichinin no samurai', 1954, 207, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Star Wars', 1977, 121, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Silence of the Lambs', 1991, 118, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Se7en', 1995, 127, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['La vita è bella', 1997, 116, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Green Mile', 1999, 189, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Saving Private Ryan', 1998, 169, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Sen to Chihiro no kamikakushi', 2001, 125, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Cidade de Deus', 2002, 130, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Interstellar', 2014, 169, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Gisaengchung', 2019, 132, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['City Lights', 1931, 87, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Modern Times', 1936, 87, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Casablanca', 1942, 102, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Psycho', 1960, 109, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['C\'era una volta il West', 1968, 165, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Back to the Future', 1985, 116, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Hotaru no haka', 1988, 89, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Nuovo Cinema Paradiso', 1988, 155, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Terminator 2: Judgment Day', 1991, 137, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Lion King', 1994, 88, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Léon', 1994, 110, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Usual Suspects', 1995, 106, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['American History X', 1998, 119, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Gladiator', 2000, 155, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Pianist', 2002, 150, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Departed', 2006, 151, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Prestige', 2006, 130, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Intouchables', 2011, 112, [$genres['Biographie'], $genres['Comédie'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Whiplash', 2014, 106, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Joker', 2019, 122, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Great Dictator', 1940, 125, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Sunset Blvd.', 1950, 110, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Rear Window', 1954, 112, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Paths of Glory', 1957, 88, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Witness for the Prosecution', 1957, 116, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Dr. Strangelove or: How I Learned to Stop Worrying and Love the Bomb', 1964, 95, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Alien', 1979, 117, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Apocalypse Now', 1979, 147, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Shining', 1980, 146, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Raiders of the Lost Ark', 1981, 115, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Once Upon a Time in America', 1984, 229, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Mononoke-hime', 1997, 134, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Memento', 2000, 113, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Oldeuboi', 2003, 120, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Das Leben der Anderen', 2006, 137, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['WALL·E', 2008, 98, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Taare Zameen Par', 2007, 165, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['3 Idiots', 2009, 170, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Dark Knight Rises', 2012, 164, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Django Unchained', 2012, 165, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Coco', 2017, 105, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Avengers: Infinity War', 2018, 149, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Avengers: Endgame', 2019, 181, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Spider-Man: Into the Spider-Verse', 2018, 117, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Dangal', 2016, 161, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Kimi no na wa.', 2016, 106, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Capharnaüm', 2018, 126, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Kid', 1921, 68, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Metropolis', 1927, 153, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['M - Eine Stadt sucht einen Mörder', 1931, 117, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Citizen Kane', 1941, 119, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Double Indemnity', 1944, 107, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Ladri di biciclette', 1948, 89, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Ikiru', 1952, 143, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Singin\' in the Rain', 1952, 103, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Vertigo', 1958, 128, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['North by Northwest', 1959, 136, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Apartment', 1960, 125, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Lawrence of Arabia', 1962, 228, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['Per qualche dollaro in più', 1965, 132, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['2001: A Space Odyssey', 1968, 149, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['A Clockwork Orange', 1971, 136, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0111161' => ['The Sting', 1973, 129, [$genres['Drame']], 'description', 13, false, new Movie()],
            'tt0075314' => ['Taxi Driver', 1976, 114, [$genres['Crime'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0082096' => ['Das Boot', 1981, 149, [$genres['Guerre'], $genres['Thriller'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0086190' => ['Star Wars: Episode VI - Return of the Jedi', 1983, 131, [$genres['Action'], $genres['Aventure'], $genres['Fantasy']], 'description', 13, false, new Movie()],
            'tt0086250' => ['Scarface', 1983, 170, [$genres['Crime'], $genres['Drame']], 'description', 13, false, new Movie()],
            'tt0086879' => ['Amadeus', 1984, 160, [$genres['Biographie'], $genres['Drame'], $genres['Histoire']], 'description', 13, false, new Movie()]
        );

        foreach ($movies as $key => &$movie) {
            //the added movie must be added on any wednesday between last week and in two months in the future
            //due to the algorithm it can be two weeks ago but we don't care
            $date = $faker->dateTimeBetween('-2 week', '+2 months');
            //find the next wednesday
            while ($date->format('w') != 3) {
                $date->modify('+1 day');
            }
            $objMovie = new Movie();
            $objMovie->setImdbId($key);
            $objMovie->setDateAdded($date);
            $objMovie->setTitle($movie[0]);
            $objMovie->setYear($movie[1]);
            $objMovie->setDuration($movie[2]);
            foreach ($movie[3] as $genre) { //add the genres to the movie
                $objMovie->addGenre($genre);
            }
            $objMovie->setDescription($movie[4]);
            $objMovie->setMinimumAge($movie[5]);
            $objMovie->setIsTeamFavorite($faker->boolean(20));
            $manager->persist($objMovie);
            $movie[7] = $objMovie;
            //Visitors can review movies
            for ($i = 0; $i < 10; $i++) {
                $review = new Review();
                $review->setMovie($movie[7]);
                $review->setUser($visitors[array_rand($visitors)]);
                $review->setRating($faker->numberBetween(1, 5));
                $review->setComment($faker->text(200));
                $review->setValidated($faker->boolean(80));
                $manager->persist($review);
            }
        }

        //Add the theaters
        $theaters = [
            'Nantes' => new Theater(),
            'Bordeaux' => new Theater(),
            'Paris' => new Theater(),
            'Toulouse' => new Theater(),
            'Lille' => new Theater(),
            'Charleroi' => new Theater(),
            'Liège' => new Theater()
        ];
        $orders = [];   //used later for creating QR codes
        foreach ($theaters as $key => &$theater) {
            $theater->setCity($key);
            $seats = [];
            for ($i = 1; $i <= 5; $i++) {
                $room = new Room();
                $room->setNumber('Salle ' . $i);
                $room->setCapacity(99);
                $room->setColumns(3);
                $room->setTheater($theater);
                $quality = $qualities[array_rand($qualities)];
                $room->setQuality($quality);
                for ($j = 1; $j <= 99; $j++) {
                    $seat = new Seat();
                    $seat->setNumber($j);
                    $seat->setForDisabled($faker->boolean(5));
                    $seat->setRoom($room);
                    array_push($seats, $seat);
                    $manager->persist($seat);
                }
                $manager->persist($room);
                shuffle($seats);

                // Create fake movie sessions (they are linked to rooms) and orders
                // $price = $quality->getPrice();
                for ($k = 0; $k < 5; $k++) {
                    $session = new MovieSession();
                    $session->setRoom($room);
                    $key = array_rand($movies);
                    $session->setMovie($movies[$key][7]);
                    $startDate = $faker->dateTimeBetween('-1 week', '+2 months');
                    $endDate = new $startDate;
                    $endDate->modify('+' . $movies[$key][7]->getDuration() . ' minutes');
                    $session->setStartDate($startDate);
                    $session->setEndDate($endDate);
                    $manager->persist($session);
                    for ($l = 0; $l < 5; $l++) {
                        $order = new OrderTickets();
                        $nbTickets = $faker->numberBetween(1, 5);
                        $order->setUser($visitors[array_rand($visitors)]);
                        $order->setStatus($faker->numberBetween(1, 2));
                        $order->setPrice($session->getRoom()->getQuality()->getPrice() * $nbTickets);
                        $manager->persist($order);
                        array_push($orders, $order);
                        for ($m = 0; $m < $nbTickets; $m++) {
                            $ticket = new Ticket();
                            $ticket->setMovieSession($session);
                            $ticket->setOrdertickets($order);
                            $seat = array_pop($seats);
                            $ticket->setSeat($seat);
                            $ticket->setPrice($session->getRoom()->getQuality()->getPrice());
                            $manager->persist($ticket);
                        }
                    }
                }

                //Create equipments and issues
                for ($k = 0; $k < 5; $k++) {
                    $issue = new Issue();
                    $issue->setUser($employee);
                    $issue->setRoom($room);
                    $issue->setTitle($faker->words(4, true));
                    $issue->setDescription($faker->text(200));
                    $issue->setStatus($faker->numberBetween(1, 4));
                    $issue->setDate($faker->dateTimeBetween('-3 month', 'now'));
                    $manager->persist($issue);
                    for ($l = 0; $l < 5; $l++) {
                        $equipment = new Equipment();
                        $equipment->setDescription($faker->text(200));
                        $equipment->setStatus($faker->numberBetween(1, 2));
                        $equipment->setIssue($issue);
                        $manager->persist($equipment);    
                    }
                }
            }
            $manager->persist($theater);
        }
        $manager->flush();
    }
}
