CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  guest_name VARCHAR(100) NOT NULL,
  room_number VARCHAR(10) NOT NULL,
  check_in_date DATE NOT NULL,
  check_out_date DATE NOT NULL,
  status VARCHAR(20) DEFAULT 'booked'
);
