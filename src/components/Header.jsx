import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

function Header() {
  const navigate = useNavigate();
  const [userData, setUserData] = useState({
    email: "",
    firstName: "",
    lastName: "",
    userName: "",
    userRole: "",
  });
  const [searchInput, setSearchInput] = useState("");
  const [totalEvents, setTotalEvents] = useState(0);

  useEffect(() => {
    fetchUserData();
    fetchTotalEvents();
  }, []);

  const fetchUserData = async () => {
    const token = localStorage.getItem("token");
    try {
      const response = await axios.get(
        "http://localhost/ticketon/get_profile.php",
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      if (response.data) {
        setUserData(response.data);
      }
    } catch (error) {
      console.error("Failed to fetch user data:", error);
    }
  };

  const fetchTotalEvents = async () => {
    try {
      const response = await axios.get(
        "http://localhost/ticketon/count_events.php"
      );
      if (response.data.total) {
        setTotalEvents(response.data.total);
      }
    } catch (error) {
      console.error("Failed to fetch total events:", error);
    }
  };

  const handleSearch = (event) => {
    event.preventDefault();
    navigate(`/index?query=${searchInput}`);
  };

  return (
    <header className="p-2 sticky-top bg-light">
      <div className="container d-flex align-items-center justify-content-around">
        <a href="/">{/* Placeholder for logo */}</a>
        <form style={{ width: "55%" }} onSubmit={handleSearch}>
          <div className="input-group">
            <input
              type="text"
              className="form-control"
              placeholder={`Find among ${totalEvents} events`}
              value={searchInput}
              onChange={(e) => setSearchInput(e.target.value)}
            />
            <button type="submit" className="btn btn-warning">
              Search
            </button>
          </div>
        </form>
        {userData.userRole === "organizer" || userData.userRole === "admin" ? (
          <a href="/add_event" className="btn btn-warning">
            ADD Event
          </a>
        ) : null}
        {userData.userRole === "admin" ? (
          <a href="/users" className="btn btn-warning">
            Users
          </a>
        ) : null}
        {!userData.userName ? (
          <button
            className="btn btn-warning"
            onClick={() => navigate("/login")}
          >
            Log in
          </button>
        ) : (
          <button
            className="btn btn-warning"
            onClick={() => navigate("/profile")}
          >
            {userData.lastName}
          </button>
        )}
      </div>
    </header>
  );
}

export default Header;
