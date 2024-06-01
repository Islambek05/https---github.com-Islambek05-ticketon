import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
// import IndexOrganizer from "./IndexOrganizer";
// import IndexAdmin from "./IndexAdmin";
// import IndexUser from "./IndexUser";

function Index() {
  const navigate = useNavigate();
  const [userData, setUserData] = useState({
    userRole: "",
  });

  useEffect(() => {
    fetchUserData();
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
  const renderContent = () => {
    switch (userData.userRole) {
      case "organizer":
        return (
          <button type="submit" className="btn btn-warning">
            Search
          </button>
        );
      case "admin":
        return (
          <button type="submit" className="btn btn-warning">
            Sea
          </button>
        );
      default:
        return (
          <button type="submit" className="btn btn-warning">
            Searc
          </button>
        );
    }
  };

  return <>{renderContent()}</>;
}

export default Index;
