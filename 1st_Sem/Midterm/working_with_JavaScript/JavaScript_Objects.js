// A. Make an array containing at least 5 JavaScript objects
let students = [
  { name: "Janimah", age: 21, grade: 90 },
  { name: "Vince", age: 19, grade: 85 },
  { name: "Janet", age: 17, grade: 92 },
  { name: "John", age: 20, grade: 75 },
  { name: "Sam", age: 18, grade: 88 }
];

// B. Use forEach() method to print each object in the array
console.log("\n---- Using forEach() ----");
students.forEach(function(student) {
  console.log(student);
});

// C. Use push() method to add a new object at the end
console.log("\n---- Using push() ----");
students.push({ name: "Rojean", age: 21, grade: 80 });
console.log(students);

// D. Use unshift() method to add a new object at the beginning
console.log("\n---- Using unshift() ----");
students.unshift({ name: "Lorss", age: 22, grade: 95 });
console.log(students);

// E. Use filter() method to get students with grade above 85
console.log("\n---- Using filter() -----");
let topStudents = students.filter(function(student) {
  return student.grade > 85;
});
console.log(topStudents);

// F. Use map() method to create a new array of student names
console.log("\n---- Using map() ----");
let studentNames = students.map(function(student) {
  return student.name;
});
console.log(studentNames);

// G. Use reduce() method to get the total of all grades
console.log("\n---- Using reduce() ----");
let totalGrades = students.reduce(function(total, student) {
  return total + student.grade;
}, 0);
console.log("Total Grades:", totalGrades);

// H. Use some() method to check if any student has grade below 80
console.log("\n---- Using some() ----");
let hasLowGrade = students.some(function(student) {
  return student.grade < 80;
});
console.log("Any student with grade below 80?", hasLowGrade ? "Yes" : "No");

// I. Use every() method to check if all students are 15 or younger
console.log("\n---- Using every() ----");
let allOldEnough = students.every(function(student) {
  return student.age <= 15;
});
console.log("All students are 15 or younger?", allOldEnough ? "Yes" : "No");

