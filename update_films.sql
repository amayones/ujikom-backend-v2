-- Update film posters to TMDB URLs
UPDATE films SET poster = 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg' WHERE title = 'Deadpool & Wolverine';
UPDATE films SET poster = 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg' WHERE title = 'Inside Out 2';
UPDATE films SET poster = 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg' WHERE title = 'Dune: Part Two';
UPDATE films SET poster = 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg' WHERE title = 'Wicked';
UPDATE films SET poster = 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg' WHERE title = 'Moana 2';
UPDATE films SET poster = 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg' WHERE title = 'Gladiator II';

-- Update film trailers
UPDATE films SET trailer = 'https://www.youtube.com/watch?v=73_1biulkYk' WHERE title = 'Deadpool & Wolverine';
UPDATE films SET trailer = 'https://www.youtube.com/watch?v=LEjhY15eCx0' WHERE title = 'Inside Out 2';
UPDATE films SET trailer = 'https://www.youtube.com/watch?v=Way9Dexny3w' WHERE title = 'Dune: Part Two';
UPDATE films SET trailer = 'https://www.youtube.com/watch?v=6COmYeLsz4c' WHERE title = 'Wicked';
UPDATE films SET trailer = 'https://www.youtube.com/watch?v=hDZ7y8RP5HE' WHERE title = 'Moana 2';
UPDATE films SET trailer = 'https://www.youtube.com/watch?v=nkD35yv1RM0' WHERE title = 'Gladiator II';
