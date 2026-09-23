

INSERT INTO students
(index_number, name, grade, contact_number, registration_date)
VALUES
('ST01', 'Kamal Perera', 'Grade 10', '0771234567', CURDATE()),
('ST02', 'Nimal Silva', 'Grade 11', '0712345678', CURDATE());



INSERT INTO books
(book_id, title, author, isbn, category, quantity, available_quantity)
VALUES
('B01', 'The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Classic', 3, 3),
('B02', 'Harry Potter and the Philosopher''s Stone', 'J. K. Rowling', '9780747532743', 'Fantasy', 2, 2),
('B03', 'Pride and Prejudice', 'Jane Austen', '9780141439518', 'Romantic', 4, 4),
('B04', 'The Alchemist', 'Paulo Coelho', '9780061122415', 'Fiction', 3, 3),
('B05', 'General Knowledge Book', 'Various Authors', '9780000000001', 'GK', 2, 2);

