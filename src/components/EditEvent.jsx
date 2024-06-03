import React, { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import axios from "axios";

function EditEvent() {
  const navigate = useNavigate();
  const location = useLocation();
  const eventID = new URLSearchParams(location.search).get("eventID");
  const [event, setEvent] = useState({
    EventName: "",
    EventDate: "",
    EventTime: "",
    Tickets: "",
    Location: "",
    Description: "",
    EventPoster: null,
  }); // Initialize with default values
  const [error, setError] = useState("");

  useEffect(() => {
    if (!eventID) {
      setError("Event not specified");
      return;
    }
    fetchEvent(eventID);
  }, [eventID]); // Add eventID as a dependency to refetch if it changes

  const fetchEvent = async (eventID) => {
    try {
      const response = await axios.get(
        `http://localhost/ticketon/getEvent.php?eventID=${eventID}`,
        {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        }
      );
      setEvent(response.data);
    } catch (error) {
      setError("Failed to load event. " + error.message);
    }
  };

  const handleInputChange = (e) => {
    const { name, value, files } = e.target;
    if (files) {
      setEvent((prev) => ({ ...prev, [name]: files[0] }));
    } else {
      setEvent((prev) => ({ ...prev, [name]: value }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData();
    Object.keys(event).forEach((key) => {
      if (key === "EventPoster" && event[key] instanceof File) {
        formData.append(key, event[key], event[key].name);
      } else {
        formData.append(key, event[key]);
      }
    });

    try {
      const response = await axios.post(
        `http://localhost/ticketon/editEvent.php`,
        formData,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        }
      );
      if (response.data.success) {
        navigate("/success");
      } else {
        throw new Error(response.data.error || "Unknown error");
      }
    } catch (error) {
      setError(
        `Failed to edit event. ${
          error.response ? error.response.data.error : error.message
        }`
      );
    }
  };

  const handleDeleteEvent = async (e) => {
    e.preventDefault();
    if (
      window.confirm(
        "Are you sure you want to delete this event? This action cannot be undone."
      )
    ) {
      try {
        await axios.delete(`http://localhost/ticketon/delete_event.php`, {
          data: { eventID },
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        navigate("/index");
      } catch (error) {
        setError("Failed to delete event. " + error.message);
      }
    }
  };

  if (error) return <div>Error: {error}</div>;
  if (!event) return <div>Loading...</div>;

  return (
    <div className="container mt-5">
      <h2>Edit Event: {event.EventName || "Loading..."}</h2>
      <form onSubmit={handleSubmit} encType="multipart/form-data">
        <div className="mb-3">
          {/* Repeat this pattern for each input field, ensuring each field is controlled */}
          <label htmlFor="eventName" className="form-label">
            Event Name:
          </label>
          <input
            type="text"
            id="eventName"
            name="eventName"
            className="form-control"
            value={event.EventName || ""}
            onChange={handleInputChange}
          />
          {/* Add additional fields for EventDate, EventTime, Tickets, Location, Description here */}
          <label htmlFor="eventDate" className="form-label">
            Event Name:
          </label>
          <input
            type="text"
            id="eventDate"
            name="eventDate"
            className="form-control"
            value={event.EventDate || ""}
            onChange={handleInputChange}
          />
          <label htmlFor="eventTime" className="form-label">
            Event Name:
          </label>
          <input
            type="text"
            id="eventTime"
            name="eventTime"
            className="form-control"
            value={event.EventTime || ""}
            onChange={handleInputChange}
          />
          <label htmlFor="tickets" className="form-label">
            Event Name:
          </label>
          <input
            type="text"
            id="tickets"
            name="tickets"
            className="form-control"
            value={event.Tickets || ""}
            onChange={handleInputChange}
          />
        </div>
        <button type="submit" className="btn btn-primary">
          Save Changes
        </button>
      </form>
      <form onSubmit={handleDeleteEvent}>
        <button type="submit" className="btn btn-danger">
          Delete Event
        </button>
      </form>
    </div>
  );
}

export default EditEvent;
