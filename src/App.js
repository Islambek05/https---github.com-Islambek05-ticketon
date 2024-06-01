import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Login from "./components/LogIn";
import Profile from "./components/Profile";
import Register from "./components/Register";
// import Index from "./components/Index";
// import Header from "./Header";
// import Footer from "./Footer";
import "bootstrap/dist/css/bootstrap.min.css";
import "./components/ticketon.css";

function App() {
  return (
    <Router>
      <div className="App">
        {/* <Header /> */}
        <Routes>
          {/* <Route path="/" element={<Index />} /> */}
          <Route path="/login" element={<Login />} />
          <Route path="/profile" element={<Profile />} />
          <Route path="/register" element={<Register />} />
        </Routes>
        {/* <Footer /> */}
      </div>
    </Router>
  );
}

export default App;
