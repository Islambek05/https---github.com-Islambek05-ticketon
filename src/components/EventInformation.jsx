import React, { useState, useEffect } from "react";
import axios from "axios";
import { useParams, useNavigate, useLocation } from "react-router-dom";

function EventDetails() {
  const location = useLocation();
  const navigate = useNavigate();
  const [event, setEvent] = useState(null);
  const [userData, setUserData] = useState(null);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchUserData = async () => {
      const token = localStorage.getItem("token");
      try {
        const response = await axios.get(
          "http://localhost/ticketon/get_profile.php",
          {
            headers: { Authorization: `Bearer ${token}` },
          }
        );
        setUserData(response.data);
      } catch (error) {
        console.error("Failed to fetch user data:", error);
        setError("Failed to load user data.");
      }
    };

    const fetchEvent = async () => {
      const eventID = new URLSearchParams(location.search).get("eventID");
      console.log(eventID);
      if (!eventID) {
        setError("Event not specified");
        return;
      }

      try {
        const response = await axios.get(
          `http://localhost/ticketon/getEvent.php`,
          {
            params: { eventID },
          }
        );
        setEvent(response.data);
      } catch (error) {
        console.error("Failed to fetch event:", error);
        setError("Failed to load event.");
      }
    };

    fetchUserData();
    fetchEvent();
  }, [eventID, navigate]);

  const handleBuyTickets = async (numberOfTickets) => {
    try {
      await axios.post(`http://localhost/ticketon/api/buy_tickets.php`, {
        eventID,
        numberOfTickets,
      });
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
            (userData.userRole === "organizer" ||
              userData.userRole === "admin") && (
              <>
                <a
                  href={`/edit_event/${event.eventID}`}
                  className="btn btn-warning"
                >
                  Change
                </a>
                <a
                  href={`/event_ticket/${event.eventID}`}
                  className="btn btn-warning"
                >
                  Info
                </a>
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
