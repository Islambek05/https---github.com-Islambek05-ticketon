import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate, useLocation, Link } from "react-router-dom";

function EventDetails() {
  const location = useLocation();
  const eventID = new URLSearchParams(location.search).get("eventID");
  const navigate = useNavigate();
  const [event, setEvent] = useState(null);
  const [userData, setUserData] = useState(null);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchUserData = async () => {
      const token = localStorage.getItem("token");
      if (!token) return;
      try {
        const response = await axios.get(
          "http://localhost/ticketon/get_profile.php",
          { headers: { Authorization: `Bearer ${token}` } }
        );
        setUserData(response.data);
      } catch (error) {
        console.error("Failed to fetch user data:", error);
        setError("Failed to load user data.");
      }
    };

    if (eventID) {
      fetchUserData();
      fetchEvent(eventID);
    } else {
      setError("Event not specified");
    }
  }, [eventID]);

  const fetchEvent = async (eventID) => {
    const token = localStorage.getItem("token");
    try {
      const url =
        userData && userData.userRole === "organizer"
          ? `http://localhost/ticketon/getOrgEvent.php?eventID=${eventID}`
          : `http://localhost/ticketon/getEvent.php?eventID=${eventID}`;
      const response = await axios.get(url, {
        headers: { Authorization: `Bearer ${token}` },
      });
      setEvent(response.data);
    } catch (error) {
      console.error("Failed to fetch event:", error);
      setError("Failed to load event.");
    }
  };

  const handleBuyTickets = async (numberOfTickets) => {
    const token = localStorage.getItem("token");
    if (!userData) {
      navigate("/login");
      return;
    }
    try {
      await axios.post(
        `http://localhost/ticketon/api/buy_tickets.php`,
        { eventID, numberOfTickets },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      navigate("/success");
    } catch (error) {
      console.error("Failed to buy tickets:", error);
      setError("Failed to buy tickets.");
    }
  };

  if (error) return <div>Error: {error}</div>;
  if (!event) return <div>Loading...</div>;

  return (
    <div className="container mt-5">
      <div className="row">
        <div className="col-md-auto">
          <img
            src={`data:image/jpeg;base64,${event.EventPoster}`}
            alt={`${event.EventName} Poster`}
            className="card-img-info rounded"
          />
        </div>
        <div className="col">
          <h1>{event.EventName}</h1>
          <p>
            <strong>Date:</strong> {event.EventDate}
          </p>
          <p>
            <strong>Time:</strong> {event.EventTime}
          </p>
          <p>
            <strong>Tickets:</strong> {event.Tickets}
          </p>
          <p>
            <strong>Location:</strong> {event.Location}
          </p>
          <p>
            <strong>Description:</strong> {event.Description}
          </p>
          {userData &&
            ((userData.userRole === "organizer" &&
              userData.userName === event.Organizer) ||
              userData.userRole === "admin") && (
              <>
                <Link
                  to={`/EditEvent?eventID=${event.EventID}`}
                  className="btn btn-warning mb-3"
                >
                  Edit
                </Link>
                <br />
                <Link
                  to={`/event_ticket/${event.EventID}`}
                  className="btn btn-warning"
                >
                  Info
                </Link>
              </>
            )}
          {userData &&
            userData.userRole !== "admin" &&
            userData.userRole !== "organizer" && (
              <button
                onClick={() => handleBuyTickets(1)}
                className="btn btn-warning"
              >
                Buy Ticket
              </button>
            )}
        </div>
      </div>
    </div>
  );
}

export default EventDetails;
