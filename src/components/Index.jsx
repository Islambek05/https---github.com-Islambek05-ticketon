import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate, useLocation } from "react-router-dom";
import IndexUser from "./IndexUser";
import IndexAdm from "./IndexAdm";
import IndexOrg from "./IndexOrg";

function Index() {
  const location = useLocation();
  const [searchInput, setSearchInput] = useState("");
  const [userData, setUserData] = useState("");
  const [events, setEvents] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    const params = new URLSearchParams(location.search);
    const query = params.get("query");
    if (query) {
      setSearchInput(query);
      handleSearch(query);
    }
    fetchUserData();
    allEvents();
  }, []);

  const fetchUserData = async () => {
    const token = localStorage.getItem("token");
    if (token) {
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
    }
  };

  const allEvents = async () => {
    try {
      const response = await axios.post(
        `http://localhost/ticketon/getAllEvents.php`,
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      );
      if (response.data) {
        setEvents(response.data);
      }
    } catch (error) {
      console.error("Failed to fetch events:", error);
      setEvents(null);
    }
  };

  const handleSearch = async (query) => {
    if (!query.trim()) return;
    try {
      const response = await axios.post(
        "http://localhost/ticketon/getEventsBySearch.php",
        { query },
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
        }
      );
      if (response.data.events) {
        setEvents(response.data.events);
      }
    } catch (error) {
      console.error("Failed to fetch events:", error);
    }
  };

  const renderContent = () => {
    switch (userData.userRole) {
      case "organizer":
        return <IndexOrg events={events} />;
      case "admin":
        return <IndexAdm events={events} />;
      default:
        return <IndexUser events={events} />;
    }
  };

  return <div>{renderContent()}</div>;
}

export default Index;
