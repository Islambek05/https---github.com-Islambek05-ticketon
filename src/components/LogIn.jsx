import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";

function Login() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [successMessage, setSuccessMessage] = useState("");

  useEffect(() => {
    if (localStorage.getItem("token")) {
      navigate("/profile");
    }
  }, [navigate]);

  const handleLogin = async (event) => {
    event.preventDefault();
    setError("");
    setSuccessMessage("");
    if (validateForm()) {
      try {
        const response = await axios.post(
          "http://localhost/ticketon/login.php",
          { email, password },
          { headers: { "Content-Type": "application/json" } }
        );
        if (response.data.success) {
          localStorage.setItem("token", response.data.token);
          setSuccessMessage("Login successful!");
          navigate("/profile");
        } else {
          setError(response.data.error);
        }
      } catch (err) {
        setError(err.response?.data?.error || "Failed to login");
      }
    }
  };

  const validateForm = () => {
    if (!email || !password) {
      setError("Both email and password fields are required.");
      return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setError("Please enter a valid email address.");
      return false;
    }
    return true;
  };

  return (
    <div className="container mt-5">
      <div className="form-signin w-100 m-auto">
        <form onSubmit={handleLogin}>
          <h1 className="h3 mb-3 fw-normal">Please Log In</h1>
          <div className="form-floating mb-3">
            <input
              type="email"
              className="form-control"
              id="Email"
              name="Email"
              placeholder="name@example.com"
              autoComplete="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />
            <label htmlFor="Email">Email address</label>
          </div>
          <div className="form-floating mb-3">
            <input
              type="password"
              className="form-control"
              id="Password"
              name="Password"
              placeholder="Password"
              autoComplete="current-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
            <label htmlFor="Password">Password</label>
          </div>
          {error && (
            <div className="alert alert-danger" role="alert">
              {error}
            </div>
          )}
          {successMessage && (
            <div className="alert alert-success" role="alert">
              {successMessage}
            </div>
          )}
          <small className="d-flex mb-2">
            <a href="/register">Registration</a>
          </small>
          <button className="btn btn-lg btn-warning w-100" type="submit">
            Log in
          </button>
        </form>
      </div>
    </div>
  );
}

export default Login;
