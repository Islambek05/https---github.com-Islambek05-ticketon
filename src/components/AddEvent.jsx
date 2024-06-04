import React, { useState, useEffect, usContext } from "react";
import axios from "axios";
import { useNavigate, useLocation } from "react-router-dom";

function AddEvent() {
  const navigate = useNavigate();
  const location = useLocation();
  const eventID = new URLSearchParams(location.search).get("eventID");
  const [eventData, setEventData] = useState(null);
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
      if (
        !userData ||
        (userData.userRole !== "admin" && userData.userRole !== "organizer")
      ) {
        navigate("/login");
      }
      fetchEvent(eventID);
    } else {
      setError("Event not specified");
    }
  }, [eventID]);

  const fetchEvent = async (eventID) => {
    try {
      const response = await axios.get(
        `http://localhost/ticketon/getEvent.php?eventID=${eventID}`
      );
      setEventData(response.data);
    } catch (error) {
      setError("Failed to load event. " + error.message);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setEventData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleFileChange = (e) => {
    setEventData((prev) => ({
      ...prev,
      eventPoster: e.target.files[0],
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData();
    Object.entries(eventData).forEach(([key, value]) => {
      formData.append(key, value);
    });

    try {
      await axios.post("http://localhost/ticketon/add_event.php", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      navigate("/events");
    } catch (err) {
      setError(err.response?.data?.error || "Failed to add event");
    }
  };

  return (
    <div className="container mt-5">
      <h2>Add New Event</h2>
      <form onSubmit={handleSubmit} encType="multipart/form-data">
        <div className="mb-3">
          <label htmlFor="eventName" className="form-label">
            Event Name:
          </label>
          <input
            type="text"
            id="eventName"
            name="eventName"
            className="form-control"
            value={eventData.eventName}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label htmlFor="eventDate" className="form-label">
            Event Date:
          </label>
          <input
            type="date"
            id="eventDate"
            name="eventDate"
            className="form-control"
            value={eventData.eventDate}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label htmlFor="eventTime" className="form-label">
            Event Time:
          </label>
          <input
            type="time"
            id="eventTime"
            name="eventTime"
            className="form-control"
            value={eventData.eventTime}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label htmlFor="eventTickets" className="form-label">
            Tickets:
          </label>
          <input
            type="number"
            id="eventTickets"
            name="eventTickets"
            className="form-control"
            value={eventData.eventTickets}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label htmlFor="location" className="form-label">
            Location:
          </label>
          <input
            type="text"
            id="location"
            name="location"
            className="form-control"
            value={eventData.location}
            onChange={handleChange}
            required
          />
        </div>
        <div className="mb-3">
          <label htmlFor="description" className="form-label">
            Description:
          </label>
          <textarea
            id="description"
            name="description"
            className="form-control"
            value={eventData.description}
            onChange={handleChange}
            required
          ></textarea>
        </div>
        <div className="mb-3">
          <label htmlFor="eventPoster" className="form-label">
            Event Poster:
          </label>
          <input
            type="file"
            id="eventPoster"
            name="eventPoster"
            accept="image/*"
            onChange={handleFileChange}
            required
          />
        </div>
        {error && <div className="alert alert-danger">{error}</div>}
        <button type="submit" className="btn btn-primary">
          Add Event
        </button>
      </form>
    </div>
  );
}

export default AddEvent;
